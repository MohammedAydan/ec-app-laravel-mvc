<?php

namespace App\Http\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class OrderService
{
    public function getMyOrders($userId, $perPage, $page): array
    {
        try {
            $baseQuery = Order::where('user_id', $userId);

            $totalSpend = (clone $baseQuery)->sum('total_amount');
            $pendingCount = (clone $baseQuery)->where('order_status', 'pending')->count();
            $completedCount = (clone $baseQuery)->whereIn('order_status', ['completed', 'delivered'])->count();
            $failedCount = (clone $baseQuery)->whereIn('payment_status', ['failed', 'canceled', 'cancelled'])->count();

            $orders = (clone $baseQuery)
                ->with('orderItems.item')
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);

            return  [
                'orders' => $orders,
                'totalSpend' => $totalSpend,
                'pendingCount' => $pendingCount,
                'completedCount' => $completedCount,
                'failedCount' => $failedCount,
            ];
        } catch (\Throwable $th) {
            Log::error('Error fetching user orders', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not fetch orders at this time.');
        }
    }

    public function getOrderById($orderId, $userId)
    {
        try {
            return Order::where('id', $orderId)->where('user_id', $userId)->with('orderItems.item')->first();
        } catch (\Throwable $th) {
            Log::error('Error fetching order by ID', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not fetch order at this time.');
        }
    }

    public function create($userId, $shippingAddress = null)
    {
        try {

            $cartItems = Cart::where('user_id', $userId)->with('item')->get();
            if (!$cartItems || $cartItems->isEmpty()) {
                throw new \Exception('No items in the order');
            }

            $totalAmount = $cartItems->sum(function ($cart) {
                $price = $cart->item?->sale_price ?? $cart->item?->price ?? 0;
                return $cart->quantity * $price;
            });

            $order = Order::create([
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'shipping_address' => $shippingAddress ?? 'N/A',
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

            return $order;
        } catch (\Throwable $th) {
            Log::error('Error creating order', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not create order at this time.');
        }
    }

    public function pay($order): ?array
    {
        try {
            // Skip payment if total is zero
            if ($order->total_amount <= 0) {
                throw new \Exception('Order total amount is zero, no payment required.');
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

            return [
                'response' => $response,
                'orderId' => $order->id,
            ];
        } catch (\Throwable $th) {
            Log::error('Error processing order payment', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not process order payment at this time.');
        }
    }

    public function createOrderPayment($userId, $orderId): ?array
    {
        try {
            $order = Order::where('id', $orderId)->where('user_id', $userId)->first();

            if (!$order) {
                throw new \Exception('Order not found.');
            }

            return $this->pay($order);
        } catch (\Throwable $th) {
            Log::error('Error creating order payment', [
                'message' => $th->getMessage()
            ]);

            throw new \Exception('Could not create order payment at this time.');
        }
    }
}
