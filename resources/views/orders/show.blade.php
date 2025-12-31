<x-app-layout>
    <x-slot name="header">
        <div class="bg-slate-900 text-white -mx-6 -mt-6 px-6 pb-8 pt-8 sm:-mx-8 sm:px-8">
            <div class="max-w-5xl mx-auto flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Order</p>
                    <h1 class="text-3xl sm:text-4xl font-black leading-tight">
                        #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h1>
                    <p class="text-slate-300 text-sm">Placed {{ $order->created_at?->format('M d, Y') }}</p>
                </div>
                <div class="flex flex-col sm:items-end gap-2 text-sm">
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full font-semibold bg-indigo-100 text-indigo-700">{{ ucfirst($order->order_status) }}</span>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full font-semibold bg-emerald-50 text-emerald-700">Payment:
                        {{ ucfirst($order->payment_status) }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-6">
            <!-- Summary cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Total</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">${{ number_format($order->total_amount, 2) }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Items</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">{{ $order->orderItems->sum('quantity') }}</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Arrival</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">
                        {{ $order->arrival_date?->format('M d, Y') ?? 'TBD' }}</p>
                    <p class="text-xs text-slate-500">Digital items deliver instantly</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
                    <p class="text-xs uppercase tracking-[0.15em] text-slate-500">Shipping</p>
                    <p class="mt-2 text-sm text-slate-800">{{ $order->shipping_address ?: 'N/A' }}</p>
                </div>
            </div>

            <!-- Items list -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900">Items</h2>
                    <p class="text-sm text-slate-500">License keys and download links available after payment.</p>
                </div>
                @forelse ($order->orderItems as $orderItem)
                    @php
                        $item = $orderItem->item;
                        $lineTotal = $orderItem->price * $orderItem->quantity;
                    @endphp
                    <div class="px-6 py-4 flex gap-4 border-b border-slate-100 last:border-0">
                        <div class="w-20 h-20 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="{{ $item?->image_url }}" alt="{{ $item?->name }}"
                                class="w-full h-full object-cover" loading="lazy" />
                        </div>
                        <div class="flex-1 flex flex-col gap-1">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-base font-semibold text-slate-900">
                                        {{ $item?->name ?? 'Item removed' }}</p>
                                    <p class="text-sm text-slate-500 line-clamp-2">{{ $item?->description }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-slate-500">Each</p>
                                    <p class="text-lg font-bold text-slate-900">
                                        ${{ number_format($orderItem->price, 2) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-slate-600">Qty: {{ $orderItem->quantity }}</p>
                                <p class="text-sm font-semibold text-slate-900">${{ number_format($lineTotal, 2) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-600">No items found for this order.</div>
                @endforelse
            </div>

            <!-- Actions -->
            {{-- <div class="flex items-center justify-end">
                <p>Payment must be completed within 1 hour.</p>
            </div> --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <a href="{{ route('orders.index') }}"
                    class="inline-flex items-center gap-2 text-slate-700 hover:text-indigo-600 font-semibold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to orders
                </a>
                <div class="flex gap-3">
                    {{-- <button
                        class="inline-flex items-center justify-center px-4 py-2 sm:rounded-3xl border border-slate-200 text-slate-700 hover:border-slate-300 transition">Download
                        invoice</button>
                    <button
                        class="inline-flex items-center justify-center px-4 py-2 sm:rounded-3xl bg-slate-900 text-white hover:bg-indigo-600 transition">Contact
                        support</button> --}}
                    {{-- @if ($order->created_at && now()->diffInHours($order->created_at) > 1) --}}
                    @if ($order->payment_status !== 'paid' && $order->order_status !== 'cancelled')
                        <form method="POST" action="{{ route('orders.payment', ['orderId' => $order->id]) }}"
                            onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 sm:rounded-3xl bg-emerald-600 text-white hover:bg-emerald-700 transition disabled:opacity-50">Complete
                                Payment</button>
                        </form>
                    @endif
                    {{-- @endif --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
