@section('title', 'Storefront | ' . config('app.name', 'Store'))
@section('meta')
    <meta name="description" content="Browse curated tech and lifestyle products with instant checkout.">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="Storefront | {{ config('app.name', 'Store') }}">
    <meta property="og:description" content="Discover fresh drops, thoughtful picks, and clean shopping experience.">
@endsection

<x-app-layout>
    <x-slot name="header">
        <div class="bg-slate-900 text-white -mx-6 -mt-6 px-6 pb-10 pt-8 sm:-mx-8 sm:px-8">
            <div class="max-w-7xl mx-auto flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-300">Curated tech & lifestyle</p>
                    <h1 class="text-3xl sm:text-4xl font-black leading-tight">Storefront</h1>
                    <p class="mt-2 text-slate-300">Fresh drops, thoughtful picks, clean experience.</p>
                </div>
                <div
                    class="flex items-center gap-3 text-sm bg-slate-800/70 border border-slate-700 rounded-full px-4 py-2 shadow-lg">
                    <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-slate-200">{{ count($items) }} products live</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
            {{-- search  --}}
            <div class="w-full ">
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <div class="mt-4">
                    {{-- <x-input-label for="search" :value="__('Search')" /> --}}

                    <form method="GET" action="{{ route('store.index') }}" class="w]-full">
                        <div class="flex items-center justify-between">
                            <x-text-input id="search" class="block w-full" type="text" name="search" required
                                autocomplete="off" placeholder="Search" value="{{ request('search') }}" />
                            <x-primary-button class="ms-3 py-3">
                                {{ __('Search') }}
                            </x-primary-button>
                        </div>
                    </form>

                    <x-input-error :messages="$errors->get('search')" class="mt-2" />
                </div>
            </div>
            <!-- Filters / Pills -->
            <div class="flex flex-wrap gap-3">
                @php $pills = ['All', 'Featured', 'On Sale', 'Audio', 'Accessories', 'Work', 'Gaming']; @endphp
                @foreach ($pills as $pill)
                    <button
                        class="px-4 py-2 rounded-full text-sm font-medium border border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:shadow-sm transition">{{ $pill }}</button>
                @endforeach
            </div>

            <!-- Product Grid -->
            <div class="grid gap-8 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($items as $item)
                    <a href="{{ route('store.show', ['slug' => $item->slug]) }}" class="group relative">
                        <div
                            class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl transition transform hover:-translate-y-1 duration-300 h-full flex flex-col">
                            <div class="relative aspect-[4/5] overflow-hidden rounded-t-2xl bg-slate-100">
                                {{-- <img src="{{ $item->image_url }}" alt="{{ $item->name }}" --}}
                                <img src="{{ $item->image_preview_url }}" alt="{{ $item->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                    loading="lazy" />
                                @if ($item->sale_price)
                                    <span
                                        class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500 text-white shadow">Sale</span>
                                @endif
                                @if ($item->stock < 15)
                                    <span
                                        class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500 text-white shadow">Low</span>
                                @endif
                            </div>

                            <div class="p-5 flex-1 flex flex-col gap-4">
                                <div class="space-y-1">
                                    <h3
                                        class="text-lg font-semibold text-slate-900 line-clamp-1 group-hover:text-indigo-600 transition">
                                        {{ $item->name }}</h3>
                                    <p class="text-sm text-slate-500 line-clamp-2">{{ $item->description }}</p>
                                </div>

                                @if ($item->tags)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach (array_slice($item->tags, 0, 2) as $tag)
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-auto flex items-center justify-between">
                                    <div class="flex items-baseline gap-2">
                                        @if ($item->sale_price)
                                            <span
                                                class="text-xl font-bold text-slate-900">${{ number_format($item->sale_price, 2) }}</span>
                                            <span
                                                class="text-sm text-slate-400 line-through">${{ number_format($item->price, 2) }}</span>
                                        @else
                                            <span
                                                class="text-xl font-bold text-slate-900">${{ number_format($item->price, 2) }}</span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-500">{{ $item->sales_count }} sold</span>
                                </div>

                                <button
                                    class="w-full mt-3 inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 text-white px-4 py-2.5 text-sm font-semibold hover:bg-indigo-600 transition">
                                    View details
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($items->hasPages())
                <div class="border-t border-slate-200 pt-6 flex flex-col gap-4">
                    <div class="text-sm text-slate-600">
                        Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }} products
                    </div>
                    <div class="flex justify-center">
                        {{ $items->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
