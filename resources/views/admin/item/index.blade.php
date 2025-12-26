@section('title', 'Items | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Admin · Catalog</p>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Items</h2>
            </div>
            <div class="flex gap-3 items-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-slate-600 font-semibold">Dashboard</a>
                @if ($canCreate ?? false)
                    <a href="{{ route('admin.items.create') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-indigo-600">Add item</a>
                @else
                    <span class="text-xs text-slate-500">Create permission required</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl">
                @unless($canRead ?? false)
                    <div class="p-8 text-center text-amber-700 bg-amber-50 border-b border-amber-100">
                        You have read-only access to items. Contact an administrator for more permissions.
                    </div>
                @endunless

                @if ($items->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-50">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3">Price</th>
                                    <th class="px-6 py-3">Stock</th>
                                    <th class="px-6 py-3">Slug</th>
                                    <th class="px-6 py-3">Created</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-800">
                                @foreach ($items as $item)
                                    <tr>
                                        <td class="px-6 py-4 font-semibold">{{ $item->name }}</td>
                                        <td class="px-6 py-4">${{ number_format($item->sale_price ?? $item->price, 2) }}</td>
                                        <td class="px-6 py-4">{{ $item->stock }}</td>
                                        <td class="px-6 py-4 text-slate-500">{{ $item->slug }}</td>
                                        <td class="px-6 py-4 text-slate-500">{{ $item->created_at?->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 flex justify-end gap-3 text-sm">
                                            @if ($canRead ?? false)
                                                <a href="{{ route('admin.items.show', $item->id) }}" class="text-indigo-600 font-semibold">View</a>
                                            @endif
                                            @if ($canUpdate ?? false)
                                                <a href="{{ route('admin.items.edit', $item->id) }}" class="text-slate-600">Edit</a>
                                            @endif
                                            @if ($canDelete ?? false)
                                                <a href="{{ route('admin.items.delete', $item->id) }}" class="text-rose-600">Delete</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($items->hasPages())
                        <div class="border-t border-slate-100 px-6 py-4">
                            {{ $items->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-8 text-center text-slate-500">
                        <p class="mb-3">No items yet.</p>
                        @if ($canCreate ?? false)
                            <a href="{{ route('admin.items.create') }}" class="text-indigo-600 font-semibold">Create your first item</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
