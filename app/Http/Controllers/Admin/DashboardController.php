<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $user?->load('role.permissions');
        $permissionNames = $user?->role?->permissions->pluck('name')->values();

        $range = $request->string('range', 'last_6_months');
        [$startDate, $endDate] = $this->resolveDateRange($range, $request);

        $ordersQuery = $this->applyDateFilter(Order::query(), $startDate, $endDate);

        $totalRevenue = (clone $ordersQuery)->sum('total_amount');
        $ordersCount = (clone $ordersQuery)->count();
        $customersCount = (clone $ordersQuery)->distinct('user_id')->count('user_id');
        $productsCount = Item::count();
        $avgOrderValue = $ordersCount > 0 ? round($totalRevenue / $ordersCount, 2) : 0;

        $recentOrders = (clone $ordersQuery)
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        $topItems = OrderItem::selectRaw('item_id, SUM(quantity) as qty, SUM(price * quantity) as revenue')
            ->whereHas('order', function ($query) use ($startDate, $endDate) {
                $this->applyDateFilter($query, $startDate, $endDate);
            })
            ->groupBy('item_id')
            ->with('item')
            ->orderByDesc('qty')
            ->take(5)
            ->get();

        $driver = DB::connection()->getDriverName();
        $monthExpression = match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $ordersByMonth = $this->applyDateFilter(Order::query(), $startDate, $endDate)
            ->selectRaw("{$monthExpression} as month, SUM(total_amount) as revenue, COUNT(*) as orders")
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        $ordersByStatus = $this->applyDateFilter(Order::query(), $startDate, $endDate)
            ->selectRaw('order_status, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('order_status')
            ->orderByDesc('count')
            ->get();

        $paymentBreakdown = $this->applyDateFilter(Order::query(), $startDate, $endDate)
            ->selectRaw('payment_status, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('payment_status')
            ->orderByDesc('count')
            ->get();

        $topCustomers = $this->applyDateFilter(Order::selectRaw('user_id, COUNT(*) as orders, SUM(total_amount) as revenue'), $startDate, $endDate)
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('revenue')
            ->take(5)
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
            'range' => $range,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    private function applyDateFilter($query, ?Carbon $startDate, ?Carbon $endDate)
    {
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query;
    }

    private function resolveDateRange(string $range, Request $request): array
    {
        $now = Carbon::now();

        return match ($range) {
            '24h' => [$now->copy()->subDay(), $now],
            'last_week' => [$now->copy()->subDays(7), $now],
            'last_month' => [$now->copy()->subDays(30), $now],
            'prev_month' => [
                $now->copy()->subMonthNoOverflow()->startOfMonth(),
                $now->copy()->subMonthNoOverflow()->endOfMonth(),
            ],
            'last_6_months' => [$now->copy()->subMonthsNoOverflow(6), $now],
            'last_year' => [$now->copy()->subYear(), $now],
            'custom' => $this->resolveCustomRange($request, $now),
            default => [$now->copy()->subMonthsNoOverflow(6), $now],
        };
    }

    private function resolveCustomRange(Request $request, Carbon $now): array
    {
        $start = $request->date('start_date')?->startOfDay();
        $end = $request->date('end_date')?->endOfDay();

        if ($start && $end && $start->greaterThan($end)) {
            [$start, $end] = [$end, $start];
        }

        return [
            $start ?? $now->copy()->subMonthsNoOverflow(6),
            $end ?? $now,
        ];
    }
}
