@section('title', 'Delete Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Admin · Orders</p>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Delete order</h2>
            </div>
            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm text-indigo-600 font-semibold">Back</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 space-y-4">
                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-slate-900">Are you sure you want to delete this order?</h3>
                    <p class="text-slate-600">This action cannot be undone.</p>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 space-y-1 text-sm text-slate-700">
                    <p><span class="font-semibold">Order:</span> #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                    <p><span class="font-semibold">Customer:</span> {{ $order->user?->name ?? 'Guest' }} ({{ $order->user?->email ?? 'N/A' }})</p>
                    <p><span class="font-semibold">Total:</span> ${{ number_format($order->total_amount, 2) }}</p>
                    <p><span class="font-semibold">Order status:</span> {{ ucfirst($order->order_status) }}</p>
                    <p><span class="font-semibold">Payment status:</span> {{ ucfirst($order->payment_status) }}</p>
                    <p><span class="font-semibold">Shipping:</span> {{ $order->shipping_address ?: 'N/A' }}</p>
                    <p><span class="font-semibold">Arrival:</span> {{ $order->arrival_date?->format('Y-m-d') ?? 'TBD' }}</p>
                </div>

                <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" class="flex items-center justify-between" @if(!($canDelete ?? false)) onsubmit="return false;" @endif>
                    @csrf
                    @method('DELETE')
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm text-slate-600">Cancel</a>
                    @if ($canDelete ?? false)
                        <button type="submit" class="rounded-lg bg-rose-600 text-white px-5 py-2 text-sm font-semibold hover:bg-rose-700">Delete</button>
                    @else
                        <button type="button" class="rounded-lg bg-slate-200 text-slate-500 px-5 py-2 text-sm font-semibold cursor-not-allowed" disabled>Delete</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
