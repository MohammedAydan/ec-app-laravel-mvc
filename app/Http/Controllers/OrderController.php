<?php

namespace App\Http\Controllers;

use App\Http\Services\OrderService;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    public function index(Request $request)
    {
        try {
            $userId = $request->user()->id;

            if ($userId == null) {
                return redirect()->route('login');
            }

            $page = max((int) $request->input('page', 1), 1);
            $perPage = (int) $request->input('limit', 10);
            if ($perPage < 1) {
                $perPage = 10;
            }
            $perPage = min($perPage, 50);

            $ordersData = $this->orderService->getMyOrders($userId, $perPage, $page);
            return view('orders.index',  $ordersData);
        } catch (\Throwable $th) {
            return view('server-error', ['code' => 500, 'message' => $th->getMessage()]);
        }
    }

    public function show(Request $request, $orderId)
    {
        try {
            $userId = $request->user()->id;

            if ($userId == null) {
                return redirect()->route('login');
            }

            $order = $this->orderService->getOrderById($orderId, $userId);

            if (!$order) {
                abort(404, 'Order not found');
            }

            return view('orders.show', ['order' => $order]);
        } catch (\Throwable $th) {
            return view('server-error', ['code' => 404, 'message' => $th->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            $userId = $request->user()->id;

            if ($userId == null) {
                return redirect()->route('login');
            }

            $order = $this->orderService->create($userId, $request->input('shipping_address'));
            return $this->pay($order);
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors('Could not create order at this time.')
                ->with('error', $th->getMessage());
        }
    }

    public function createOrderPayment(Request $request, $orderId)
    {
        try {
            $userId = $request->user()->id;

            if ($userId == null) {
                return redirect()->route('login');
            }

            $order = $this->orderService->getOrderById($orderId, $userId);

            if (!$order) {
                abort(404, 'Order not found');
            }

            return $this->pay($order);
        } catch (\Throwable $th) {
            return redirect()->back()
                ->withErrors('Could not process payment at this time.')
                ->with('error', $th->getMessage());
        }
    }

    public function pay($order)
    {
        try {
            // Skip payment if total is zero
            if ($order->total_amount <= 0) {
                return redirect()->route('orders.show', ['orderId' => $order->id]);
            }

            $result = $this->orderService->pay($order);
            $response = $result['response'] ?? null;
            $orderId = $result['orderId'] ?? null;

            try {
                // Guard against missing/failed response
                if (!is_array($response) || empty($response['links'])) {
                    return redirect()->route('orders.show', ['orderId' => $orderId])
                        ->with('status', 'Payment could not be initiated.');
                }

                foreach ($response['links'] as $link) {
                    if (($link['rel'] ?? null) === 'approve' && !empty($link['href'])) {
                        return redirect()->away($link['href']);
                    }
                }

                return redirect()->route('orders.show', ['orderId' => $orderId])
                    ->with('status', 'Payment link not available.');
            } catch (\Throwable $e) {
                return redirect()->route('orders.show', ['orderId' => $orderId])
                    ->with('status', 'Payment could not be initiated: ' . $e->getMessage());
            }
        } catch (\Throwable $th) {
            return redirect()->route('orders.show', ['orderId' => $order->id])
                ->withErrors('Could not process payment at this time.')
                ->with('error', $th->getMessage());
        }
    }
}
