<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        if ($userId == null) {
            return redirect()->route('login');
        }

        $orders = Order::where('user_id', $userId)->with('orderItems.item')->get();
        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Request $request, $orderId)
    {
        $userId = $request->user()->id;

        if ($userId == null) {
            return redirect()->route('login');
        }

        $order = Order::where('id', $orderId)->where('user_id', $userId)->with('orderItems.item')->first();

        if (!$order) {
            abort(404, 'Order not found');
        }

        return view('orders.show', ['order' => $order]);
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;

        if ($userId == null) {
            return redirect()->route('login');
        }

        $cartItems = Cart::where('user_id', $userId)->with('item')->get();
        if (!$cartItems || $cartItems->isEmpty()) {
            abort(400, 'No items in the order');
        }
        $totalAmount = $cartItems->sum(function ($cart) {
            $price = $cart->item?->sale_price ?? $cart->item?->price ?? 0;
            return $cart->quantity * $price;
        });

        $order = Order::create([
            'user_id' => $userId,
            'total_amount' => $totalAmount,
            'shipping_address' => $request->input('shipping_address') ?? 'N/A',
            'order_status' => 'pending',
            'payment_status' => 'pending',
            'arrival_date' => null,
        ]);

        foreach ($cartItems as $item) {
            $price = $item->item?->sale_price ?? $item->item?->price ?? 0;
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item->item_id,
                'quantity' => $item->quantity,
                'price' => $price,
            ]);
        }

        // Clear the cart after creating the order
        Cart::where('user_id', $userId)->delete();
        // return redirect()->route('orders.show', ['orderId' => $order->id]);
        return $this->pay($order);
    }

    public function createOrderPayment(Request $request, $orderId)
    {
        $userId = $request->user()->id;

        if ($userId == null) {
            return redirect()->route('login');
        }

        $order = Order::where('id', $orderId)->where('user_id', $userId)->first();

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'Order not found.');
        }

        return $this->pay($order);
    }

    public function pay($order)
    {
        // Skip payment if total is zero
        if ($order->total_amount <= 0) {
            return redirect()->route('orders.show', ['orderId' => $order->id]);
        }

        // PayPal provider
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        // Create PayPal Order
        $response = $provider->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'reference_id' => (string) $order->id,
                'amount' => [
                    'currency_code' => 'USD',
                    'value' => number_format($order->total_amount, 2, '.', '')
                ]
            ]],
            'application_context' => [
                'return_url' => route('paypal.payment.success'),
                'cancel_url' => route('paypal.payment.cancel'),
            ],
        ]);

        try {
            // Guard against missing/failed response
            if (!is_array($response) || empty($response['links'])) {
                return redirect()->route('orders.show', ['orderId' => $order->id])
                    ->with('status', 'Payment could not be initiated.');
            }

            foreach ($response['links'] as $link) {
                if (($link['rel'] ?? null) === 'approve' && !empty($link['href'])) {
                    return redirect()->away($link['href']);
                }
            }

            return redirect()->route('orders.show', ['orderId' => $order->id])
                ->with('status', 'Payment link not available.');
        } catch (\Throwable $e) {
            return redirect()->route('orders.show', ['orderId' => $order->id])
                ->with('status', 'Payment could not be initiated: ' . $e->getMessage());
        }
    }

}
