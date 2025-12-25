@section('title', 'Dashboard | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Overview</p>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Dashboard</h2>
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-500">
                <a href="{{ route('admin.roles.index') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-slate-700 hover:border-slate-300 hover:text-slate-900">
                    Manage roles
                </a>
                <span>Updated {{ now()->format('M d, Y') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Revenue</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">${{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Orders</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $ordersCount }}</p>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Customers</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $customersCount }}</p>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Avg order</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">${{ number_format($avgOrderValue, 2) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Orders -->
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm lg:col-span-2">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Recent</p>
                            <h3 class="text-lg font-bold text-slate-900">Latest orders</h3>
                        </div>
                        <a class="text-sm text-indigo-600 font-semibold" href="{{ route('orders.index') }}">View all</a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($recentOrders as $order)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-slate-900">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->created_at?->format('M d, Y') }} • {{ ucfirst($order->payment_status) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-slate-900">${{ number_format($order->total_amount, 2) }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->user?->name ?? 'Guest' }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-slate-500">No orders yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Top products -->
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Products</p>
                        <h3 class="text-lg font-bold text-slate-900">Top sellers</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($topItems as $row)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-900">{{ $row->item?->name ?? 'Item removed' }}</p>
                                    <p class="text-xs text-slate-500">{{ $row->qty }} sold</p>
                                </div>
                                <div class="text-right text-sm font-semibold text-slate-900">
                                    ${{ number_format($row->revenue, 2) }}
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-slate-500">No sales yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Inventory summary -->
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Catalog</p>
                        <h3 class="text-lg font-bold text-slate-900">Products snapshot</h3>
                    </div>
                    <span class="text-sm text-slate-500">Total: {{ $productsCount }}</span>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-slate-700">
                    <div class="bg-slate-50 rounded-xl p-4">All products: <span class="font-semibold text-slate-900">{{ $productsCount }}</span></div>
                    <div class="bg-slate-50 rounded-xl p-4">Orders: <span class="font-semibold text-slate-900">{{ $ordersCount }}</span></div>
                    <div class="bg-slate-50 rounded-xl p-4">Customers: <span class="font-semibold text-slate-900">{{ $customersCount }}</span></div>
                    <div class="bg-slate-50 rounded-xl p-4">Revenue: <span class="font-semibold text-slate-900">${{ number_format($totalRevenue, 2) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
