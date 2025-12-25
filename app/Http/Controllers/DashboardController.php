<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $roleName = $user?->role?->name;
        if (!in_array($roleName, ['admin', 'owner'])) {
            // abort(403);
            return redirect()->route('store.index');
        }

        $user->load('role.permissions');
        $permissionNames = $user->role?->permissions->pluck('name')->values();

        $totalRevenue = Order::sum('total_amount');
        $ordersCount = Order::count();
        $customersCount = User::count();
        $productsCount = Item::count();
        $avgOrderValue = $ordersCount > 0 ? round($totalRevenue / $ordersCount, 2) : 0;

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        $topItems = OrderItem::selectRaw('item_id, SUM(quantity) as qty, SUM(price * quantity) as revenue')
            ->groupBy('item_id')
            ->with('item')
            ->orderByDesc('qty')
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
            'permissionNames' => $permissionNames,
        ]);
    }
}
