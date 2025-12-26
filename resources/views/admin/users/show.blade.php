@section('title', $user->name . ' | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">Back</a>
                <span class="text-slate-300">/</span>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">{{ $user->name }}</h2>
            </div>
            <div class="flex gap-3 items-center">
                @if ($canUpdate ?? false)
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-sm text-slate-600 font-semibold">Edit</a>
                    <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-sm {{ $user->is_active ? 'text-amber-600' : 'text-emerald-600' }} font-semibold">
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                @endif
                @if ($canDelete ?? false)
                    <a href="{{ route('admin.users.delete', $user->id) }}" class="text-sm text-rose-600 font-semibold">Delete</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 space-y-4">
                <div class="flex items-center gap-3">
                    @if ($user->is_active)
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Active</span>
                    @else
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Inactive</span>
                    @endif
                    <span class="text-slate-500 text-sm">Joined {{ $user->created_at?->format('Y-m-d') }}</span>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Name</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Email</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Role</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $user->role->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Verified</p>
                        <p class="text-lg font-semibold text-slate-900">{{ $user->email_verified_at ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
