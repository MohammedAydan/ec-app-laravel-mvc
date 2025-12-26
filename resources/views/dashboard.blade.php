@section('title', 'Dashboard | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="font-bold text-xl text-slate-900">Dashboard</h2>
                <span class="text-xs text-slate-500">{{ now()->format('M d, Y') }}</span>
            </div>
            <div class="flex items-center gap-2">
                @if ($canRead ?? false)
                    <a href="{{ route('admin.orders.index') }}" class="text-xs text-slate-600 hover:text-slate-900 px-2 py-1 rounded hover:bg-slate-100">Orders</a>
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-slate-600 hover:text-slate-900 px-2 py-1 rounded hover:bg-slate-100">Users</a>
                    <a href="{{ route('admin.items.index') }}" class="text-xs text-slate-600 hover:text-slate-900 px-2 py-1 rounded hover:bg-slate-100">Items</a>
                    <a href="{{ route('admin.roles.index') }}" class="text-xs text-slate-600 hover:text-slate-900 px-2 py-1 rounded hover:bg-slate-100">Roles</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Toolbar -->
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm px-4 py-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <form method="GET" action="{{ route('dashboard') }}" id="range-form" class="flex flex-wrap items-center gap-3">
                        <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Period</span>
                        <select name="range" id="range-select"
                            class="text-sm text-slate-800 border border-slate-200 bg-white rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400">
                            <option value="24h" @selected($range === '24h')>Last 24 hours</option>
                            <option value="last_week" @selected($range === 'last_week')>Last week</option>
                            <option value="last_month" @selected($range === 'last_month')>Last month</option>
                            <option value="prev_month" @selected($range === 'prev_month')>Previous month</option>
                            <option value="last_6_months" @selected($range === 'last_6_months')>Last 6 months</option>
                            <option value="last_year" @selected($range === 'last_year')>Last year</option>
                            <option value="custom" @selected($range === 'custom')>Custom</option>
                        </select>
                        <div id="custom-date-fields" class="items-center gap-2 {{ $range === 'custom' ? 'flex' : 'hidden' }}">
                            <input type="date" name="start_date" value="{{ optional($startDate)->format('Y-m-d') }}"
                                class="text-sm border border-slate-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-indigo-100" />
                            <span class="text-slate-400 text-xs">to</span>
                            <input type="date" name="end_date" value="{{ optional($endDate)->format('Y-m-d') }}"
                                class="text-sm border border-slate-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-indigo-100" />
                        </div>
                        <button class="rounded-lg bg-indigo-600 px-4 py-1.5 text-white text-sm font-medium hover:bg-indigo-700 transition-colors">
                            Apply
                        </button>
                    </form>
                    <form method="GET" action="{{ route('admin.users.index') }}" class="hidden md:flex items-center gap-2">
                        <input name="q" type="text" placeholder="Search users..."
                            class="text-sm border border-slate-200 rounded-lg px-3 py-1.5 w-48 focus:ring-2 focus:ring-indigo-100" value="{{ request('q') }}" />
                        <button class="text-indigo-600 text-sm font-medium hover:text-indigo-800">Search</button>
                    </form>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Revenue</p>
                        <div class="h-8 w-8 rounded-full bg-emerald-50 flex items-center justify-center">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-2xl font-bold text-slate-900">${{ number_format($totalRevenue, 2) }}</p>
                    <p class="mt-1 text-xs text-slate-500">Total in period</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Orders</p>
                        <div class="h-8 w-8 rounded-full bg-indigo-50 flex items-center justify-center">
                            <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-2xl font-bold text-slate-900">{{ $ordersCount }}</p>
                    <p class="mt-1 text-xs text-slate-500">Completed orders</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Customers</p>
                        <div class="h-8 w-8 rounded-full bg-sky-50 flex items-center justify-center">
                            <svg class="h-4 w-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-2xl font-bold text-slate-900">{{ $customersCount }}</p>
                    <p class="mt-1 text-xs text-slate-500">Active buyers</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Avg Order</p>
                        <div class="h-8 w-8 rounded-full bg-amber-50 flex items-center justify-center">
                            <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-3 text-2xl font-bold text-slate-900">${{ number_format($avgOrderValue, 2) }}</p>
                    <p class="mt-1 text-xs text-slate-500">Per transaction</p>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="xl:col-span-2 space-y-6">
                    <!-- Revenue Trend -->
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-slate-900">Revenue Trend</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Monthly breakdown</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">Month</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">Orders</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">Revenue</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wide">Avg</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($ordersByMonth as $row)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-5 py-3 font-medium text-slate-900">{{ $row->month }}</td>
                                            <td class="px-5 py-3 text-slate-600">{{ $row->orders }}</td>
                                            <td class="px-5 py-3 text-slate-600">${{ number_format($row->revenue, 2) }}</td>
                                            <td class="px-5 py-3 text-slate-600">${{ $row->orders > 0 ? number_format($row->revenue / $row->orders, 2) : '0.00' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="px-5 py-8 text-center text-slate-500">No data available</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-slate-900">Recent Orders</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Latest 5 transactions</p>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View all →</a>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse ($recentOrders as $order)
                                <div class="px-5 py-3 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">
                                            #{{ $order->id }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-900 text-sm">{{ $order->user?->name ?? 'Guest' }}</p>
                                            <p class="text-xs text-slate-500">{{ $order->created_at?->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-slate-900 text-sm">${{ number_format($order->total_amount, 2) }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $order->payment_status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <div class="px-5 py-8 text-center text-slate-500">No orders yet</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Order Status -->
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="font-semibold text-slate-900">Order Status</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Distribution overview</p>
                        </div>
                        <div class="p-4 space-y-3">
                            @forelse ($ordersByStatus as $status)
                                @php
                                    $colors = ['pending' => 'bg-amber-500', 'processing' => 'bg-blue-500', 'completed' => 'bg-emerald-500', 'cancelled' => 'bg-red-500'];
                                    $color = $colors[$status->order_status] ?? 'bg-slate-400';
                                    $percent = $ordersCount > 0 ? round(($status->count / $ordersCount) * 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1">
                                        <span class="font-medium text-slate-700">{{ ucfirst($status->order_status) }}</span>
                                        <span class="text-slate-500">{{ $status->count }} ({{ $percent }}%)</span>
                                    </div>
                                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="{{ $color }} h-full rounded-full transition-all" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-slate-500 py-4">No data</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="font-semibold text-slate-900">Payment Status</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Payment breakdown</p>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse ($paymentBreakdown as $payment)
                                <div class="px-5 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full {{ $payment->payment_status === 'completed' ? 'bg-emerald-500' : ($payment->payment_status === 'pending' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                        <span class="text-sm font-medium text-slate-700">{{ ucfirst($payment->payment_status) }}</span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-slate-900">${{ number_format($payment->revenue, 2) }}</p>
                                        <p class="text-xs text-slate-500">{{ $payment->count }} orders</p>
                                    </div>
                                </div>
                            @empty
                                <div class="px-5 py-8 text-center text-slate-500">No data</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Top Products -->
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="font-semibold text-slate-900">Top Products</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Best sellers</p>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse ($topItems as $index => $row)
                                <div class="px-5 py-3 flex items-center gap-3">
                                    <div class="h-7 w-7 rounded-full bg-indigo-50 flex items-center justify-center text-xs font-bold text-indigo-600">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-900 truncate">{{ $row->item_name ?? 'Removed' }}</p>
                                        <p class="text-xs text-slate-500">{{ $row->qty }} sold</p>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900">${{ number_format($row->revenue, 2) }}</p>
                                </div>
                            @empty
                                <div class="px-5 py-8 text-center text-slate-500">No sales yet</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Top Customers -->
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100">
                            <h3 class="font-semibold text-slate-900">Top Customers</h3>
                            <p class="text-xs text-slate-500 mt-0.5">By revenue</p>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @forelse ($topCustomers as $customer)
                                <div class="px-5 py-3 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-slate-900">{{ $customer->user_name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-slate-500">{{ $customer->orders }} orders</p>
                                    </div>
                                    <p class="text-sm font-semibold text-emerald-600">${{ number_format($customer->revenue, 2) }}</p>
                                </div>
                            @empty
                                <div class="px-5 py-8 text-center text-slate-500">No customers yet</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Stats -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <div class="flex flex-wrap items-center justify-center gap-8 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600">Products:</span>
                        <span class="font-semibold text-slate-900">{{ $productsCount }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                        <span class="text-slate-600">Orders:</span>
                        <span class="font-semibold text-slate-900">{{ $ordersCount }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                        <span class="text-slate-600">Customers:</span>
                        <span class="font-semibold text-slate-900">{{ $customersCount }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        <span class="text-slate-600">Revenue:</span>
                        <span class="font-semibold text-slate-900">${{ number_format($totalRevenue, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('range-select');
        const customFields = document.getElementById('custom-date-fields');

        const toggle = () => {
            const isCustom = select?.value === 'custom';
            if (!customFields) return;
            customFields.classList.toggle('hidden', !isCustom);
            customFields.classList.toggle('flex', isCustom);
        };

        select?.addEventListener('change', toggle);
        toggle();
    });
</script>
