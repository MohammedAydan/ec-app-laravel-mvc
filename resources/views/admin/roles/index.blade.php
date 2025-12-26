@section('title', 'Roles & Permissions | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Admin</p>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Roles & Permissions</h2>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 font-semibold">Back to dashboard</a>
        </div>
    </x-slot>

    @php
        $permissions = ($permissionNames ?? collect())->filter();
        $canCreate = $permissions->contains('create');
        $canRead = $permissions->contains('read');
        $canUpdate = $permissions->contains('update');
        $canDelete = $permissions->contains('delete');
    @endphp

<div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @unless ($canRead)
                <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm">
                    You have read-only access to this page.
                </div>
            @endunless

            <!-- Assign role to user -->
            @if ($canUpdate)
                <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Assign role to user</h3>
                    <form class="grid gap-3 sm:grid-cols-3" method="POST" action="{{ route('admin.roles.assign') }}">
                        @csrf
                        <input name="user_email" type="email" class="sm:col-span-2 border rounded-lg px-3 py-2 text-sm"
                            placeholder="User email" required />
                        <select name="role_id" class="sm:col-span-1 border rounded-lg px-3 py-2 text-sm" required>
                            <option value="">Select role</option>
                            @foreach ($roles as $roleOption)
                                <option value="{{ $roleOption->id }}">{{ $roleOption->name }}</option>
                            @endforeach
                        </select>
                        <div class="sm:col-span-3 flex justify-end">
                            <button
                                class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-indigo-600">Assign</button>
                        </div>
                    </form>
                </div>
            @endif

            @if ($canCreate)
                <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Create role</h3>
                    <form class="grid gap-3 sm:grid-cols-3" method="POST" action="{{ route('admin.roles.store') }}">
                        @csrf
                        <input name="name" class="sm:col-span-1 border rounded-lg px-3 py-2 text-sm" placeholder="Name"
                            required />
                        <input name="description" class="sm:col-span-2 border rounded-lg px-3 py-2 text-sm"
                            placeholder="Description (optional)" />
                        <div class="sm:col-span-3 flex justify-end">
                            <button
                                class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-indigo-600">Save</button>
                        </div>
                    </form>
                    @error('name')
                        <p class="text-sm text-rose-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl divide-y divide-slate-100">
                @forelse ($roles as $role)
                    <div class="p-6 space-y-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-base font-semibold text-slate-900">{{ $role->name }}</p>
                                <p class="text-sm text-slate-500">{{ $role->description ?? 'No description' }}</p>
                            </div>
                            @if ($canDelete)
                                <form method="POST" action="{{ route('admin.roles.delete', $role) }}"
                                    onsubmit="return confirm('Delete this role?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-rose-600 hover:text-rose-700">Delete</button>
                                </form>
                            @endif
                        </div>

                        <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-slate-800">Permissions</p>
                                @if ($canCreate)
                                    <form class="flex flex-wrap gap-2" method="POST"
                                        action="{{ route('admin.permissions.store', $role) }}">
                                        @csrf
                                        <select name="name" class="border rounded-lg px-3 py-2 text-sm" required>
                                            <option value="">Select</option>
                                            <option value="create">create</option>
                                            <option value="read">read</option>
                                            <option value="update">update</option>
                                            <option value="delete">delete</option>
                                        </select>
                                        <input name="description" class="border rounded-lg px-3 py-2 text-sm"
                                            placeholder="Description (optional)" />
                                        <button
                                            class="rounded-lg bg-slate-900 text-white px-3 py-2 text-sm font-semibold hover:bg-indigo-600">Add</button>
                                    </form>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($role->permissions as $perm)
                                    @if ($canDelete)
                                        <form method="POST"
                                            action="{{ route('admin.permissions.delete', [$role, $perm]) }}"
                                            onsubmit="return confirm('Delete permission?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 rounded-full bg-white border border-slate-200 text-xs font-semibold text-slate-700 hover:border-rose-200 hover:text-rose-600">
                                                {{ $perm->name }}
                                            </button>
                                        </form>
                                    @else
                                        <span
                                            class="px-3 py-1 rounded-full bg-white border border-slate-200 text-xs font-semibold text-slate-400 cursor-not-allowed">
                                            {{ $perm->name }}
                                        </span>
                                    @endif
                                @empty
                                    <p class="text-sm text-slate-500">No permissions yet.</p>
                                @endforelse
                            </div>
                        </div>

                        @if ($canUpdate)
                            <details class="text-sm text-slate-600">
                                <summary class="cursor-pointer text-slate-700 font-semibold">Edit role</summary>
                                <form class="mt-3 grid gap-3 sm:grid-cols-3" method="POST"
                                    action="{{ route('admin.roles.update', $role) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input name="name" value="{{ $role->name }}"
                                        class="sm:col-span-1 border rounded-lg px-3 py-2 text-sm" required />
                                    <input name="description" value="{{ $role->description }}"
                                        class="sm:col-span-2 border rounded-lg px-3 py-2 text-sm" />
                                    <div class="sm:col-span-3 flex justify-end">
                                        <button
                                            class="rounded-lg bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-indigo-600">Update</button>
                                    </div>
                                </form>
                            </details>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-slate-500">No roles defined yet.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
