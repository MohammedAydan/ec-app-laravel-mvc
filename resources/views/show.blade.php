@section('title', $item->name . ' | ' . config('app.name', 'Store'))
@section('meta')
    <meta name="description" content="{{ Str::limit($item->description, 150) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $item->name }} | {{ config('app.name', 'Store') }}">
    <meta property="og:description" content="{{ Str::limit($item->description, 150) }}">
    <meta property="og:image" content="{{ $item->image_url }}">
@endsection

<x-app-layout>
    <x-slot name="header">
        <div class="bg-slate-900 text-white -mx-6 -mt-6 px-6 pb-8 pt-8 sm:-mx-8 sm:px-8">
            <div class="max-w-7xl mx-auto flex items-center gap-4">
                <a href="/store" class="inline-flex items-center text-slate-300 hover:text-white transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>
                <span class="text-slate-500">/</span>
                <h1 class="text-2xl sm:text-3xl font-black leading-tight">{{ $item->name }}</h1>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">
                <!-- Gallery -->
                <div class="lg:col-span-3 space-y-4">
                    <div class="relative rounded-3xl overflow-hidden bg-slate-200 aspect-[4/3] shadow-xl">
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                            class="w-full h-full object-cover" />
                        @if ($item->sale_price)
                            <span
                                class="absolute top-4 left-4 px-4 py-2 rounded-full text-xs font-semibold bg-rose-500 text-white shadow">Sale</span>
                        @endif
                    </div>
                </div>

                <!-- Details -->
                <div class="lg:col-span-2 flex flex-col gap-8">
                    <div class="space-y-3">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Product</p>
                        <h2 class="text-3xl font-bold text-slate-900">{{ $item->name }}</h2>
                        <p class="text-slate-600 leading-relaxed">{{ $item->description }}</p>
                        @if ($item->tags)
                            <div class="flex flex-wrap gap-2 pt-2">
                                @foreach ($item->tags as $tag)
                                    <span
                                        class="px-3 py-1 text-xs font-semibold bg-indigo-50 text-indigo-600 rounded-full">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-2xl bg-white border border-slate-100 p-4">
                            <p class="text-xs text-slate-500">Sales</p>
                            <p class="text-xl font-bold text-slate-900">{{ $item->sales_count }}</p>
                        </div>
                        <div class="rounded-2xl bg-white border border-slate-100 p-4">
                            <p class="text-xs text-slate-500">Stock</p>
                            @if ($item->stock > 50)
                                <p class="text-xl font-bold text-emerald-600">In stock</p>
                            @elseif($item->stock > 0)
                                <p class="text-xl font-bold text-amber-600">Low ({{ $item->stock }})</p>
                            @else
                                <p class="text-xl font-bold text-rose-600">Out</p>
                            @endif
                        </div>
                        <div class="rounded-2xl bg-white border border-slate-100 p-4">
                            <p class="text-xs text-slate-500">Rating</p>
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-bold text-slate-900">4.8</span>
                                <span class="text-amber-400">★★★★★</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-white border border-slate-100 p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            @if ($item->sale_price)
                                <span
                                    class="text-4xl font-bold text-slate-900">${{ number_format($item->sale_price, 2) }}</span>
                                <span
                                    class="text-base text-slate-400 line-through">${{ number_format($item->price, 2) }}</span>
                            @else
                                <span
                                    class="text-4xl font-bold text-slate-900">${{ number_format($item->price, 2) }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col gap-3">
                            <form method="POST"
                                action="{{ route('store.cart.store', ['itemId' => $item->id, 'quantity' => 1]) }}">
                                @csrf
                                <button
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 text-white px-6 py-4 text-sm font-semibold hover:bg-indigo-600 transition {{ $item->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $item->stock == 0 ? 'disabled' : '' }}>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l1 4h12l1.5-3H21" />
                                    </svg>
                                    Add to cart
                                </button>
                            </form>

                            {{-- <button
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 text-slate-700 px-6 py-3 text-sm font-semibold hover:border-slate-300 transition">
                                Save for later
                            </button> --}}
                        </div>
                        <div class="text-sm text-slate-500 space-y-1">
                            <p>Free shipping over $50 • 30-day returns • Secure checkout</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
