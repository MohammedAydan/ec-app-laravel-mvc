<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Load user permissions
        $user = $request->user();
        $user?->load('role.permissions');
        $permissionNames = $user?->role?->permissions->pluck('name')->values() ?? collect();
        $canRead = $permissionNames->contains('read');

        // Resolve date range
        $range = $request->input('range', 'last_6_months');
        [$startDate, $endDate] = $this->resolveDateRange($range, $request);

        // Base metrics
        $totalRevenue = Order::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount');
        $ordersCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $customersCount = Order::whereBetween('created_at', [$startDate, $endDate])->distinct('user_id')->count('user_id');
        $productsCount = Item::count();
        $avgOrderValue = $ordersCount > 0 ? round($totalRevenue / $ordersCount, 2) : 0;

        // Recent orders
        $recentOrders = Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        // Top selling items
        $topItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('items', 'order_items.item_id', '=', 'items.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'order_items.item_id',
                'items.name as item_name',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('order_items.item_id', 'items.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        // Orders by month (driver-aware)
        $driver = DB::connection()->getDriverName();
        $monthExpression = match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $ordersByMonth = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw("{$monthExpression} as month"), DB::raw('SUM(total_amount) as revenue'), DB::raw('COUNT(*) as orders'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Orders by status
        $ordersByStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('order_status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('order_status')
            ->orderByDesc('count')
            ->get();

        // Payment breakdown
        $paymentBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_status', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('payment_status')
            ->orderByDesc('count')
            ->get();

        // Top customers
        $topCustomers = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'orders.user_id',
                'users.name as user_name',
                DB::raw('COUNT(*) as orders'),
                DB::raw('SUM(orders.total_amount) as revenue')
            )
            ->groupBy('orders.user_id', 'users.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'totalRevenue' => $totalRevenue,
            'ordersCount' => $ordersCount,
            'customersCount' => $customersCount,
            'productsCount' => $productsCount,
            'avgOrderValue' => $avgOrderValue,
            'recentOrders' => $recentOrders,
            'topItems' => $topItems,
            'ordersByMonth' => $ordersByMonth,
            'ordersByStatus' => $ordersByStatus,
            'paymentBreakdown' => $paymentBreakdown,
            'topCustomers' => $topCustomers,
            'permissionNames' => $permissionNames,
            'canRead' => $canRead,
            'range' => $range,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    private function resolveDateRange(string $range, Request $request): array
    {
        $now = Carbon::now();

        return match ($range) {
            '24h' => [
                $now->copy()->subDay()->startOfDay(),
                $now->copy()->endOfDay(),
            ],
            'last_week' => [
                $now->copy()->subDays(7)->startOfDay(),
                $now->copy()->endOfDay(),
            ],
            'last_month' => [
                $now->copy()->subDays(30)->startOfDay(),
                $now->copy()->endOfDay(),
            ],
            'prev_month' => [
                $now->copy()->subMonthNoOverflow()->startOfMonth()->startOfDay(),
                $now->copy()->subMonthNoOverflow()->endOfMonth()->endOfDay(),
            ],
            'last_6_months' => [
                $now->copy()->subMonths(6)->startOfDay(),
                $now->copy()->endOfDay(),
            ],
            'last_year' => [
                $now->copy()->subYear()->startOfDay(),
                $now->copy()->endOfDay(),
            ],
            'custom' => $this->resolveCustomRange($request, $now),
            default => [
                $now->copy()->subMonths(6)->startOfDay(),
                $now->copy()->endOfDay(),
            ],
        };
    }

    private function resolveCustomRange(Request $request, Carbon $now): array
    {
        $start = $request->date('start_date');
        $end = $request->date('end_date');

        if ($start && $end && $start->greaterThan($end)) {
            [$start, $end] = [$end, $start];
        }

        return [
            $start ? $start->copy()->startOfDay() : $now->copy()->subMonths(6)->startOfDay(),
            $end ? $end->copy()->endOfDay() : $now->copy()->endOfDay(),
        ];
    }
}
