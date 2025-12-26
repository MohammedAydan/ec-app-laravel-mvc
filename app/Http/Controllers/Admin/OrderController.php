<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    // Admin: listing with search
    public function index(Request $request)
    {
        $limit = min((int) $request->input('limit', 20), 200);
        $page = (int) $request->input('page', 1);
        $q = trim((string) $request->input('q', ''));
        $field = $request->input('field', 'all');
        $field = in_array($field, ['id', 'email', 'name', 'order_status', 'payment_status', 'all'], true) ? $field : 'all';

        $ordersQuery = Order::with(['user', 'orderItems.item'])->latest();

        if ($q !== '') {
            $ordersQuery->where(function ($query) use ($q, $field) {
                switch ($field) {
                    case 'id':
                        $query->where('id', (int) $q);
                        break;
                    case 'email':
                        $query->whereHas('user', fn($u) => $u->where('email', 'like', "%$q%"));
                        break;
                    case 'name':
                        $query->whereHas('user', fn($u) => $u->where('name', 'like', "%$q%"));
                        break;
                    case 'order_status':
                        $query->where('order_status', 'like', "%$q%");
                        break;
                    case 'payment_status':
                        $query->where('payment_status', 'like', "%$q%");
                        break;
                    default:
                        $query->where(function ($sub) use ($q) {
                            $sub->where('id', (int) $q)
                                ->orWhere('order_status', 'like', "%$q%")
                                ->orWhere('payment_status', 'like', "%$q%")
                                ->orWhereHas('user', function ($u) use ($q) {
                                    $u->where('name', 'like', "%$q%")
                                        ->orWhere('email', 'like', "%$q%")
                                        ->orWhere('id', (int) $q);
                                });
                        });
                        break;
                }
            });
        }

        $orders = $ordersQuery->paginate($limit, ['*'], 'page', $page)->appends($request->query());

        return view('admin.orders.index', compact('orders'));
    }

    // Admin: show
    public function show($orderId)
    {
        $order = Order::with(['user', 'orderItems.item'])->findOrFail($orderId);

        return view('admin.orders.show', compact('order'));
    }

    // Admin: printable invoice view
    public function printInvoice($orderId)
    {
        $order = Order::with(['user', 'orderItems.item'])->findOrFail($orderId);

        return view('admin.orders.print', compact('order'));
    }

    // Admin: CSV report download
    public function report(Request $request)
    {
        $orders = Order::with('user')->latest()->take(500)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-report.csv"',
        ];

        $callback = function () use ($orders) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'User', 'Email', 'Total', 'Order Status', 'Payment Status', 'Placed']);
            foreach ($orders as $order) {
                fputcsv($output, [
                    $order->id,
                    $order->user?->name,
                    $order->user?->email,
                    $order->total_amount,
                    $order->order_status,
                    $order->payment_status,
                    optional($order->created_at)->format('Y-m-d H:i'),
                ]);
            }
            fclose($output);
        };

        return Response::stream($callback, 200, $headers);
    }

    // Admin: edit & update
    public function edit($orderId)
    {
        $order = Order::with(['user'])->findOrFail($orderId);
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        $validated = $request->validate([
            'order_status' => 'required|string|max:50',
            'payment_status' => 'required|string|max:50',
            'shipping_address' => 'nullable|string|max:500',
            'arrival_date' => 'nullable|date',
        ]);

        $order->order_status = $validated['order_status'];
        $order->payment_status = $validated['payment_status'];
        $order->shipping_address = $validated['shipping_address'] ?? null;
        $order->arrival_date = $validated['arrival_date'] ?? null;
        $order->save();

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order updated successfully.');
    }

    public function delete($orderId)
    {
        $order = Order::with('user')->findOrFail($orderId);
        return view('admin.orders.delete', compact('order'));
    }

    public function destroy($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
