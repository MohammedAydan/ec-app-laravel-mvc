@section('title', 'Edit Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . ' | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Admin · Orders</p>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Edit order</h2>
            </div>
            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm text-indigo-600 font-semibold">Back</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @unless ($canUpdate ?? false)
                <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm">
                    You do not have permission to update orders. Fields are read-only.
                </div>
            @endunless

            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 space-y-6">
                <div class="grid gap-4 sm:grid-cols-3 text-sm text-slate-700">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Order</p>
                        <p class="text-lg font-semibold text-slate-900">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-slate-600">Placed {{ $order->created_at?->format('Y-m-d H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Customer</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $order->user?->name ?? 'Guest' }}</p>
                        <p class="text-slate-600">{{ $order->user?->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Total</p>
                        <p class="text-lg font-semibold text-slate-900">${{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>

                @php
                    $orderStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                    $paymentStatuses = ['pending', 'unpaid', 'paid', 'refunded', 'failed'];
                @endphp

                <form method="POST" action="{{ route('admin.orders.update', $order->id) }}" class="space-y-6" @if(!($canUpdate ?? false)) onsubmit="return false;" @endif>
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Order status *</span>
                            <select name="order_status" required class="w-full border rounded-lg px-3 py-2 text-sm" @if(!($canUpdate ?? false)) disabled @endif>
                                @foreach ($orderStatuses as $status)
                                    <option value="{{ $status }}" @selected(old('order_status', $order->order_status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                                @if (!in_array(old('order_status', $order->order_status), $orderStatuses))
                                    <option value="{{ old('order_status', $order->order_status) }}" selected>
                                        {{ ucfirst(old('order_status', $order->order_status)) }}
                                    </option>
                                @endif
                            </select>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Payment status *</span>
                            <select name="payment_status" required class="w-full border rounded-lg px-3 py-2 text-sm" @if(!($canUpdate ?? false)) disabled @endif>
                                @foreach ($paymentStatuses as $status)
                                    <option value="{{ $status }}" @selected(old('payment_status', $order->payment_status) === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                                @if (!in_array(old('payment_status', $order->payment_status), $paymentStatuses))
                                    <option value="{{ old('payment_status', $order->payment_status) }}" selected>
                                        {{ ucfirst(old('payment_status', $order->payment_status)) }}
                                    </option>
                                @endif
                            </select>
                        </label>

                        <label class="space-y-2 sm:col-span-2">
                            <span class="text-sm font-semibold text-slate-800">Shipping address</span>
                            <textarea name="shipping_address" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Shipping address" @if(!($canUpdate ?? false)) disabled @endif>{{ old('shipping_address', $order->shipping_address) }}</textarea>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Arrival date</span>
                            <input type="date" name="arrival_date" value="{{ old('arrival_date', $order->arrival_date?->format('Y-m-d')) }}" class="w-full border rounded-lg px-3 py-2 text-sm" @if(!($canUpdate ?? false)) disabled @endif />
                        </label>
                    </div>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-sm text-slate-600">Cancel</a>
                        <div class="flex gap-3 items-center">
                            @if ($canDelete ?? false)
                                <a href="{{ route('admin.orders.delete', $order->id) }}" class="text-sm text-rose-600">Delete</a>
                            @endif
                            <button type="submit" class="rounded-lg bg-slate-900 text-white px-5 py-2 text-sm font-semibold hover:bg-indigo-600 @if(!($canUpdate ?? false)) opacity-60 cursor-not-allowed @endif" @if(!($canUpdate ?? false)) disabled @endif>Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>