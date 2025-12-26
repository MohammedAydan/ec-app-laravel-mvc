@section('title', 'Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">Back</a>
                <span class="text-slate-300">/</span>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h2>
            </div>
            <div class="flex gap-3 text-sm">
                @if ($canRead ?? false)
                    <a href="{{ route('admin.orders.print', $order->id) }}" class="text-slate-600">Print</a>
                @endif
                @if ($canUpdate ?? false)
                    <a href="{{ route('admin.orders.edit', $order->id) }}" class="text-slate-600">Edit</a>
                @endif
                @if ($canDelete ?? false)
                    <a href="{{ route('admin.orders.delete', $order->id) }}" class="text-rose-600">Delete</a>
                @endif
                <a href="{{ route('admin.orders.report') }}" class="text-indigo-600 font-semibold">Export</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-slate-700">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Customer</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $order->user?->name ?? 'Guest' }}</p>
                        <p class="text-slate-600">{{ $order->user?->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Order</p>
                        <p class="text-lg font-semibold text-slate-900">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-slate-600">Placed {{ $order->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Status</p>
                        <p class="font-semibold">{{ ucfirst($order->order_status) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Payment</p>
                        <p class="font-semibold">{{ ucfirst($order->payment_status) }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Shipping</p>
                        <p class="font-semibold">{{ $order->shipping_address ?: 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Arrival</p>
                        <p class="font-semibold">{{ $order->arrival_date?->format('Y-m-d') ?? 'TBD' }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="text-lg font-bold text-slate-900 mb-3">Items</h3>
                    <div class="divide-y divide-slate-100">
                        @foreach ($order->orderItems as $orderItem)
                            @php
                                $item = $orderItem->item;
                                $lineTotal = $orderItem->price * $orderItem->quantity;
                            @endphp
                            <div class="py-3 flex items-center gap-4">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0">
                                    <img src="{{ $item?->image_url }}" alt="{{ $item?->name }}" class="w-full h-full object-cover" />
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-slate-900">{{ $item?->name ?? 'Item removed' }}</p>
                                    <p class="text-xs text-slate-500">Qty: {{ $orderItem->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-slate-500">Each</p>
                                    <p class="font-semibold text-slate-900">${{ number_format($orderItem->price, 2) }}</p>
                                    <p class="text-sm font-semibold text-slate-900">${{ number_format($lineTotal, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end text-lg font-bold text-slate-900 border-t border-slate-100 pt-4">
                    Total: ${{ number_format($order->total_amount, 2) }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
