@section('title', 'Delete User | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Admin · Users</p>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Delete user</h2>
            </div>
            <a href="{{ route('admin.users.show', $user->id) }}" class="text-sm text-indigo-600 font-semibold">Back</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 space-y-4">
                <div class="space-y-2">
                    <h3 class="text-xl font-bold text-slate-900">Are you sure you want to delete this user?</h3>
                    <p class="text-slate-600">This action cannot be undone.</p>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 space-y-1 text-sm text-slate-700">
                    <p><span class="font-semibold">Name:</span> {{ $user->name }}</p>
                    <p><span class="font-semibold">Email:</span> {{ $user->email }}</p>
                    <p><span class="font-semibold">Status:</span> {{ $user->is_active ? 'Active' : 'Inactive' }}</p>
                    <p><span class="font-semibold">Role:</span> {{ $user->role->name ?? '—' }}</p>
                </div>

                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="flex items-center justify-between" @if(!($canDelete ?? false)) onsubmit="return false;" @endif>
                    @csrf
                    @method('DELETE')
                    <a href="{{ route('admin.users.show', $user->id) }}" class="text-sm text-slate-600">Cancel</a>
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
