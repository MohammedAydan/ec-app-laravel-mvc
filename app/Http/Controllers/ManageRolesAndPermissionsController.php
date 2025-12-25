<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ManageRolesAndPermissionsController extends Controller
{
    public function index()
    {
        $this->ensureAllowed(request(), 'read');

        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissionNames = request()->user()?->role?->permissions->pluck('name')->values();

        return view('admin.roles.index', [
            'roles' => $roles,
            'permissionNames' => $permissionNames,
        ]);
    }

    public function storeRole(Request $request)
    {
        $this->ensureAllowed($request, 'create');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Role::create($data);

        return back()->with('status', 'Role created');
    }

    public function updateRole(Request $request, Role $role)
    {
        $this->ensureAllowed($request, 'update');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $role->update($data);

        return back()->with('status', 'Role updated');
    }

    public function deleteRole(Role $role)
    {
        $this->ensureAllowed(request(), 'delete');

        $role->delete();

        return back()->with('status', 'Role deleted');
    }

    public function storePermission(Request $request, Role $role)
    {
        $this->ensureAllowed($request, 'create');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $exists = $role->permissions()->where('name', $data['name'])->exists();
        if ($exists) {
            return back()->with('status', 'Permission already exists for this role');
        }

        $role->permissions()->create($data);

        return back()->with('status', 'Permission added');
    }

    public function deletePermission(Role $role, Permission $permission)
    {
        $this->ensureAllowed(request(), 'delete');

        if ($permission->role_id !== $role->id) {
            abort(404);
        }

        $permission->delete();

        return back()->with('status', 'Permission removed');
    }

    public function assignRoleToUser(Request $request)
    {
        $this->ensureAllowed($request, 'update');

        $data = $request->validate([
            'user_email' => ['required', 'email'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user = User::where('email', $data['user_email'])->first();

        if (!$user) {
            return back()->with('status', 'User not found');
        }

        $user->role_id = $data['role_id'];
        $user->save();

        return back()->with('status', 'Role assigned to user');
    }

    private function ensureAllowed(Request $request, string $permission): void
    {
        $user = $request->user();
        $roleName = $user?->role?->name;

        if (!in_array($roleName, ['admin', 'owner'])) {
            abort(403);
        }

        if (!$user?->hasPermission($permission)) {
            abort(403);
        }
    }
}
