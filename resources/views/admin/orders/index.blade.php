@section('title', 'Orders | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Admin · Orders</p>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Orders</h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.orders.report') }}" class="text-sm text-slate-600 font-semibold">Export CSV</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-4">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                    <div class="flex-1 flex gap-2">
                        <select name="field" class="border rounded-lg px-3 py-2 text-sm text-slate-700">
                            <option value="all" @selected(request('field', 'all') === 'all')>All fields</option>
                            <option value="id" @selected(request('field') === 'id')>Order ID</option>
                            <option value="email" @selected(request('field') === 'email')>Customer email</option>
                            <option value="name" @selected(request('field') === 'name')>Customer name</option>
                            <option value="order_status" @selected(request('field') === 'order_status')>Order status</option>
                            <option value="payment_status" @selected(request('field') === 'payment_status')>Payment status</option>
                        </select>
                        <input name="q" value="{{ request('q') }}" placeholder="Search orders" autocomplete="off"
                            class="flex-1 border rounded-lg px-3 py-2 text-sm" />
                    </div>
                    <div class="flex gap-2 sm:justify-end">
                        <a href="{{ route('admin.orders.index') }}" class="text-sm text-slate-500 px-3 py-2">Clear</a>
                        <button class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-indigo-600">Search</button>
                    </div>
                </form>
            </div>

            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl">
                @unless($canRead ?? false)
                    <div class="p-8 text-center text-amber-700 bg-amber-50 border-b border-amber-100">
                        You have read-only access to orders. Contact an administrator for more permissions.
                    </div>
                @endunless

                @if ($orders->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    <th class="px-6 py-3">Order</th>
                                    <th class="px-6 py-3">Customer</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Payment</th>
                                    <th class="px-6 py-3">Total</th>
                                    <th class="px-6 py-3">Placed</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-800">
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4 font-semibold">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold">{{ $order->user?->name ?? 'Guest' }}</div>
                                            <div class="text-xs text-slate-500">{{ $order->user?->email }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">{{ ucfirst($order->order_status) }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">{{ ucfirst($order->payment_status) }}</span>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-slate-900">${{ number_format($order->total_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-slate-500">{{ $order->created_at?->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 flex justify-end gap-3 text-sm">
                                            @if ($canRead ?? false)
                                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 font-semibold">View</a>
                                                <a href="{{ route('admin.orders.print', $order->id) }}" class="text-slate-600">Print</a>
                                            @endif
                                            @if ($canUpdate ?? false)
                                                <a href="{{ route('admin.orders.edit', $order->id) }}" class="text-slate-600">Edit</a>
                                            @endif
                                            @if ($canDelete ?? false)
                                                <a href="{{ route('admin.orders.delete', $order->id) }}" class="text-rose-600">Delete</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($orders->hasPages())
                        <div class="border-t border-slate-100 px-6 py-4">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center text-slate-500">
                        <p class="mb-3">No orders found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
