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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($items as $item)
                    <a href="{{ route('store.show', $item->slug) }}"
                        class="group block focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-xl">

                        <article
                            class="bg-white border border-gray-200 rounded-xl overflow-hidden transition hover:shadow-md">

                            <!-- Image -->
                            <div class="aspect-[4/5] bg-gray-100 overflow-hidden">
                                <img src="{{ $item->image_preview_url }}" alt="{{ $item->name }}"
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    loading="lazy" />
                            </div>

                            <!-- Content -->
                            <div class="p-4 flex flex-col gap-3">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 line-clamp-1">
                                        {{ $item->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 line-clamp-2">
                                        {{ $item->description }}
                                    </p>
                                </div>

                                <!-- Price -->
                                <div class="flex items-center justify-between mt-auto">
                                    <div class="flex items-baseline gap-2">
                                        @if ($item->sale_price)
                                            <span class="text-base font-semibold text-gray-900">
                                                ${{ number_format($item->sale_price, 2) }}
                                            </span>
                                            <span class="text-xs text-gray-400 line-through">
                                                ${{ number_format($item->price, 2) }}
                                            </span>
                                        @else
                                            <span class="text-base font-semibold text-gray-900">
                                                ${{ number_format($item->price, 2) }}
                                            </span>
                                        @endif
                                    </div>

                                    <span class="text-xs text-gray-400">
                                        {{ $item->sales_count }} sold
                                    </span>
                                </div>
                            </div>

                        </article>
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
