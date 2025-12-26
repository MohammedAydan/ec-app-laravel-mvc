<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class ManageUsersController extends Controller
{
	public function index(Request $request)
	{
		$page = (int) $request->input('page', 1);
		$limit = min((int) $request->input('limit', 15), 100);

		$search = trim((string) $request->input('q', ''));
		$field = $request->input('field', 'all');
		$field = in_array($field, ['name', 'email', 'id', 'all'], true) ? $field : 'all';

		$usersQuery = User::with('role')->latest();

		if ($search !== '') {
			$usersQuery->where(function ($query) use ($search, $field) {
				switch ($field) {
					case 'name':
						$query->where('name', 'like', '%' . $search . '%');
						break;
					case 'email':
						$query->where('email', 'like', '%' . $search . '%');
						break;
					case 'id':
						$query->where('id', (int) $search);
						break;
					default:
						$query->where(function ($sub) use ($search) {
							$sub->where('name', 'like', '%' . $search . '%')
								->orWhere('email', 'like', '%' . $search . '%');
						});
						break;
				}
			});
		}

		$users = $usersQuery->paginate($limit, ['*'], 'page', $page)->appends($request->query());

		return view('admin.users.index', compact('users'));
	}

	public function show($id)
	{
		$user = User::with('role')->findOrFail($id);

		return view('admin.users.show', compact('user'));
	}

	public function edit($id)
	{
		$user = User::with('role')->findOrFail($id);
		$roles = Role::orderBy('name')->get();

		return view('admin.users.edit', compact('user', 'roles'));
	}

	public function update(Request $request, $id)
	{
		$user = User::findOrFail($id);

		$validated = $request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|max:255|unique:users,email,' . $user->id,
			'role_id' => 'nullable|exists:roles,id',
			'is_active' => 'sometimes|boolean',
		]);

		$user->name = $validated['name'];
		$user->email = $validated['email'];
		$user->role_id = $validated['role_id'] ?? null;
		if (array_key_exists('is_active', $validated)) {
			$user->is_active = $validated['is_active'];
		}
		$user->save();

		return redirect()->route('admin.users.show', $user->id)->with('success', 'User updated successfully.');
	}

	public function toggleStatus($id)
	{
		$user = User::findOrFail($id);
		$user->is_active = !$user->is_active;
		$user->save();

		return redirect()->back()->with('success', 'User status updated.');
	}

	public function delete($id)
	{
		$user = User::findOrFail($id);

		return view('admin.users.delete', compact('user'));
	}

	public function destroy($id)
	{
		$user = User::findOrFail($id);
		$user->delete();

		return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
	}
}
