<x-app-layout>
    <x-slot name="header">
        <div class="bg-slate-900 text-white -mx-6 -mt-6 px-6 pb-8 pt-8 sm:-mx-8 sm:px-8">
            <div class="max-w-7xl mx-auto flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Your cart</p>
                    <h1 class="text-3xl sm:text-4xl font-black leading-tight">Review & checkout</h1>
                    <p class="text-slate-300 text-sm">Downloadable items, instant delivery after payment.</p>
                </div>
                <div
                    class="flex items-center gap-3 text-sm bg-slate-800/70 border border-slate-700 rounded-full px-4 py-2 shadow-lg">
                    <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-slate-200">{{ $cartItems->count() }}
                        item{{ $cartItems->count() !== 1 ? 's' : '' }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->item->sale_price ?? ($item->item->price ?? 0);
            $qty = $item->quantity ?? ($item->pivot->quantity ?? 1);
            return $price * $qty;
        });
    @endphp

    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart items -->
            <div class="lg:col-span-2 space-y-4">
                @forelse ($cartItems as $cartItem)
                    @php
                        $qty = $cartItem->quantity ?? ($cartItem->pivot->quantity ?? 1);
                        $price = $cartItem->item->sale_price ?? ($cartItem->item->price ?? 0);
                        $lineTotal = $price * $qty;
                    @endphp
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5 flex gap-4">
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-slate-100 flex-shrink-0">
                            <img src="{{ $cartItem->item->image_url }}" alt="{{ $cartItem->item->name }}"
                                class="w-full h-full object-cover" loading="lazy" />
                        </div>
                        <div class="flex-1 flex flex-col gap-2">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-base sm:text-lg font-semibold text-slate-900">
                                        {{ $cartItem->item->name }}</h3>
                                    <p class="text-sm text-slate-500 line-clamp-2">{{ $cartItem->item->description }}
                                    </p>
                                    @if ($cartItem->item->tags)
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @foreach (array_slice($cartItem->item->tags, 0, 2) as $tag)
                                                <span
                                                    class="px-2 py-1 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-600">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-slate-500">Price</p>
                                    <p class="text-lg font-bold text-slate-900">${{ number_format($price, 2) }}</p>
                                    <form method="POST"
                                        action="{{ route('store.cart.destroy', ['cartItemId' => $cartItem->id]) }}">
                                        @csrf
                                        <button
                                            class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-rose-600 hover:text-rose-700 transition"
                                            aria-label="Remove item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 7h12M9 7l.867 10.4c.05.6.57 1.1 1.173 1.1h2.92c.603 0 1.123-.5 1.173-1.1L15 7M10 7V5.5A1.5 1.5 0 0111.5 4h1A1.5 1.5 0 0114 5.5V7" />
                                            </svg>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <form method="POST"
                                        action="{{ route('store.cart.decrement', ['cartItemId' => $cartItem->id]) }}">
                                        @csrf
                                        <button
                                            class="w-9 h-9 inline-flex items-center justify-center sm:rounded-3xl border border-slate-200 text-slate-700 hover:border-slate-300 transition"
                                            aria-label="Decrease quantity">-</button>
                                    </form>
                                    <span
                                        class="px-4 py-2 sm:rounded-3xl bg-slate-100 text-slate-800 text-sm font-semibold">{{ $qty }}</span>
                                    <form method="POST"
                                        action="{{ route('store.cart.increment', ['cartItemId' => $cartItem->id]) }}">
                                        @csrf
                                        <button
                                            class="w-9 h-9 inline-flex items-center justify-center sm:rounded-3xl border border-slate-200 text-slate-700 hover:border-slate-300 transition"
                                            aria-label="Increase quantity">+</button>
                                    </form>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-slate-500">Subtotal</p>
                                    <p class="text-lg font-bold text-slate-900">${{ number_format($lineTotal, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 text-center text-slate-600">
                        Your cart is empty. <a href="{{ route('store.index') }}"
                            class="text-indigo-600 font-semibold hover:underline">Browse products</a>.
                    </div>
                @endforelse
            </div>

            <!-- Summary -->
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.18em] text-slate-500">Summary</p>
                        <h3 class="text-2xl font-bold text-slate-900">Checkout</h3>
                    </div>
                    <div class="space-y-3 text-sm text-slate-700">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-600 font-semibold">
                            <span>Delivery</span>
                            <span>Instant (free)</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Taxes</span>
                            <span class="text-slate-500">Calculated at payment</span>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-between text-lg font-bold text-slate-900 pt-2 border-t border-slate-100">
                        <span>Total</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="space-y-2">
                        @if ($cartItems->count() > 0)
                            <form method="POST" action="{{ route('orders.store') }}"
                                onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-center rounded-xl bg-slate-900 text-white px-4 py-3 font-semibold hover:bg-indigo-600 transition disabled:opacity-50">
                                    Proceed to checkout
                                </button>
                            </form>
                        @endif
                        <p class="text-xs text-slate-500">Secure payment • Instant file access • 30-day support</p>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 space-y-2 text-sm text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>Instant download after payment</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>License for commercial use</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
                        <span>Free updates included</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
