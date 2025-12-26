@section('title', 'Create Item | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Admin · Catalog</p>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Create item</h2>
            </div>
            <a href="{{ route('admin.items.index') }}" class="text-sm text-indigo-600 font-semibold">Back to items</a>
        </div>
    </x-slot>

    @php
        $oldTagsInput = old('tags');
        $oldTagsString = is_array($oldTagsInput) ? implode(', ', $oldTagsInput) : ($oldTagsInput ?? '');
    @endphp

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @unless ($canCreate ?? false)
                <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm">
                    You do not have permission to create items. Form inputs are disabled.
                </div>
            @endunless

            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6">
                <form method="POST" action="{{ route('admin.items.store') }}" class="space-y-6" enctype="multipart/form-data" @if(!($canCreate ?? false)) onsubmit="return false;" @endif>
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Name *</span>
                            <input name="name" value="{{ old('name') }}" required @if(!($canCreate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Wireless Headphones" />
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Slug *</span>
                            <input name="slug" value="{{ old('slug') }}" required @if(!($canCreate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="wireless-headphones" />
                        </label>

                        <label class="sm:col-span-2 space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Description</span>
                            <textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Short description" @if(!($canCreate ?? false)) disabled @endif>{{ old('description') }}</textarea>
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Price *</span>
                            <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required @if(!($canCreate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="99.99" />
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Sale price</span>
                            <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price') }}" @if(!($canCreate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="79.99" />
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Currency (ISO 4217) *</span>
                            <input name="currency" value="{{ old('currency', 'USD') }}" maxlength="3" minlength="3" required @if(!($canCreate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm uppercase" placeholder="USD" />
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Stock *</span>
                            <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" required @if(!($canCreate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="25" />
                        </label>

                        <label class="sm:col-span-2 space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Tags</span>
                            <input name="tags" value="{{ $oldTagsString }}" class="w-full border rounded-lg px-3 py-2 text-sm" @if(!($canCreate ?? false)) disabled @endif
                                placeholder="audio, wireless, premium" />
                            <p class="text-xs text-slate-500">Comma-separated; we will split and clean them.</p>
                        </label>

                        <label class="sm:col-span-2 space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Image URL</span>
                            <input type="url" name="image_url" value="{{ old('image_url') }}" @if(!($canCreate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="https://.../image.jpg" />
                        </label>

                        <label class="sm:col-span-2 space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Upload image (optional)</span>
                            <input type="file" name="image" accept="image/*" @if(!($canCreate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm" />
                            <p class="text-xs text-slate-500">If both are provided, uploaded image will be used.</p>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.items.index') }}" class="text-sm text-slate-600 px-4 py-2">Cancel</a>
                        <button type="submit"
                            class="rounded-lg bg-slate-900 text-white px-5 py-2 text-sm font-semibold hover:bg-indigo-600 @if(!($canCreate ?? false)) opacity-60 cursor-not-allowed @endif" @if(!($canCreate ?? false)) disabled @endif>Save item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
