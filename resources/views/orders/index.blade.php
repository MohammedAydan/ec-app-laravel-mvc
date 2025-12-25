<x-app-layout>
    <x-slot name="header">
        <div class="bg-slate-900 text-white -mx-6 -mt-6 px-6 pb-8 pt-8 sm:-mx-8 sm:px-8">
            <div class="max-w-7xl mx-auto flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Orders</p>
                    <h1 class="text-3xl sm:text-4xl font-black leading-tight">Your purchases</h1>
                    <p class="text-slate-300 text-sm">Track status, downloadables, and delivery windows.</p>
                </div>
                <div
                    class="flex items-center gap-3 text-sm bg-slate-800/70 border border-slate-700 rounded-full px-4 py-2 shadow-lg">
                    <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-slate-200">{{ $orders->count() }}
                        order{{ $orders->count() !== 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $totalSpend = $orders->sum('total_amount');
        $pendingCount = $orders->where('order_status', 'pending')->count();
        $completedCount = $orders->whereIn('order_status', ['completed', 'delivered'])->count();
        $failedCount = $orders->whereIn('payment_status', ['failed', 'canceled', 'cancelled'])->count();
    @endphp

    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Total spend</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">${{ number_format($totalSpend, 2) }}</p>
                    <p class="text-xs text-slate-500 mt-1">Lifetime across all orders</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Completed</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-700">{{ $completedCount }}</p>
                    <p class="text-xs text-slate-500 mt-1">Delivered / fulfilled</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Pending</p>
                    <p class="mt-2 text-2xl font-bold text-amber-700">{{ $pendingCount }}</p>
                    <p class="text-xs text-slate-500 mt-1">Waiting on payment or processing</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Issues</p>
                    <p class="mt-2 text-2xl font-bold text-rose-700">{{ $failedCount }}</p>
                    <p class="text-xs text-slate-500 mt-1">Failed or canceled payments</p>
                </div>
            </div>

            <!-- Orders table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm uppercase tracking-[0.15em] text-slate-500">Orders</p>
                        <h2 class="text-xl font-bold text-slate-900">Latest activity</h2>
                    </div>
                    <span class="text-xs text-slate-500">Auto-refreshes on update</span>
                </div>

                @if ($orders->isEmpty())
                    <div class="p-10 text-center text-slate-600">
                        No orders yet. <a href="{{ route('store.index') }}"
                            class="text-indigo-600 font-semibold hover:underline">Browse
                            products</a> to get started.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-slate-700">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                                    <th class="px-6 py-3">Order</th>
                                    <th class="px-6 py-3">Items</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Payment</th>
                                    <th class="px-6 py-3">Total</th>
                                    <th class="px-6 py-3">Placed</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($orders as $order)
                                    @php
                                        $itemNames = $order->orderItems
                                            ->map(fn($oi) => optional($oi->item)->name)
                                            ->filter()
                                            ->values();
                                        $preview = $itemNames->take(2)->implode(', ');
                                        $extraCount = max($itemNames->count() - 2, 0);
                                        $statusStyles = [
                                            'pending' => 'bg-amber-100 text-amber-800',
                                            'processing' => 'bg-indigo-100 text-indigo-800',
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'delivered' => 'bg-emerald-100 text-emerald-800',
                                            'canceled' => 'bg-rose-100 text-rose-800',
                                            'cancelled' => 'bg-rose-100 text-rose-800',
                                        ];
                                        $paymentStyles = [
                                            'pending' => 'bg-amber-50 text-amber-700',
                                            'paid' => 'bg-emerald-50 text-emerald-700',
                                            'failed' => 'bg-rose-50 text-rose-700',
                                            'refunded' => 'bg-sky-50 text-sky-700',
                                        ];
                                        $arrivalLabel = $order->arrival_date
                                            ? $order->arrival_date->format('M d, Y')
                                            : 'TBD';
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 align-top">
                                            <div class="font-semibold text-slate-900">
                                                #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-xs text-slate-500">{{ $arrivalLabel }}</div>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <div class="text-slate-800">{{ $preview ?: '—' }}@if ($extraCount > 0)
                                                    <span class="text-slate-500">+{{ $extraCount }} more</span>
                                                @endif
                                            </div>
                                            <div class="text-xs text-slate-500 mt-1">
                                                {{ $order->orderItems->sum('quantity') }} items</div>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusStyles[$order->order_status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ ucfirst($order->order_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $paymentStyles[$order->payment_status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 align-top font-semibold text-slate-900">
                                            ${{ number_format($order->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 align-top text-sm text-slate-600">
                                            {{ $order->created_at?->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 align-top text-right">
                                            <a href="{{ route('orders.show', ['orderId' => $order->id]) }}"
                                                class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 font-medium transition-colors group">
                                                <span>View details</span>
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 transition-transform group-hover:translate-x-0.5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
