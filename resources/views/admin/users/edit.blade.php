@section('title', 'Edit User | ' . config('app.name', 'Store'))

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Admin · Users</p>
                <h2 class="font-black text-2xl text-slate-900 leading-tight">Edit user</h2>
            </div>
            <a href="{{ route('admin.users.show', $user->id) }}" class="text-sm text-indigo-600 font-semibold">Back</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @unless ($canUpdate ?? false)
                <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm">
                    You do not have permission to update users. Fields are read-only.
                </div>
            @endunless

            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6">
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="space-y-6" @if(!($canUpdate ?? false)) onsubmit="return false;" @endif>
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Name *</span>
                            <input name="name" value="{{ old('name', $user->name) }}" required @if(!($canUpdate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="Jane Doe" />
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Email *</span>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required @if(!($canUpdate ?? false)) disabled @endif
                                class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="jane@example.com" />
                        </label>

                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-800">Role</span>
                            <select name="role_id" class="w-full border rounded-lg px-3 py-2 text-sm" @if(!($canUpdate ?? false)) disabled @endif>
                                <option value="">No role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="flex items-center gap-2 mt-6">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) @if(!($canUpdate ?? false)) disabled @endif>
                            <span class="text-sm text-slate-800">Active</span>
                        </label>
                    </div>

                    <div class="flex justify-between items-center">
                        <a href="{{ route('admin.users.show', $user->id) }}" class="text-sm text-slate-600">Cancel</a>
                        <div class="flex gap-3 items-center">
                            @if ($canDelete ?? false)
                                <a href="{{ route('admin.users.delete', $user->id) }}" class="text-sm text-rose-600">Delete</a>
                            @endif
                            <button type="submit"
                                class="rounded-lg bg-slate-900 text-white px-5 py-2 text-sm font-semibold hover:bg-indigo-600 @if(!($canUpdate ?? false)) opacity-60 cursor-not-allowed @endif" @if(!($canUpdate ?? false)) disabled @endif>Save changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
