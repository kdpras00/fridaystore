<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(['admin', 'kasir', 'owner'])],
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('swal_success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(['admin', 'kasir', 'owner'])],
        ]);

        if ($user->id === auth()->id() && !$user->hasRole($request->role)) {
            return back()->withInput()->with('swal_error', 'Anda tidak dapat mengubah role akun sendiri.');
        }

        if ($user->hasRole('admin') && $request->role !== 'admin' && !$this->hasAnotherActiveAdmin($user)) {
            return back()->withInput()->with('swal_error', 'Minimal harus ada satu admin aktif di sistem.');
        }

        $data = ['name' => $request->name, 'email' => $request->email];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->role);

        return redirect()->route('users.index')->with('swal_success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('swal_error', 'Tidak dapat menghapus akun sendiri.');
        }

        if ($user->transaksi()->exists() || $user->stokMutasi()->exists()) {
            return back()->with('swal_error', 'User tidak dapat dihapus karena memiliki riwayat transaksi atau mutasi stok.');
        }

        if ($user->hasRole('admin') && !$this->hasAnotherActiveAdmin($user)) {
            return back()->with('swal_error', 'Admin aktif terakhir tidak dapat dihapus.');
        }

        $user->delete();
        return back()->with('swal_success', 'User berhasil dihapus.');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('swal_error', 'Tidak dapat menonaktifkan akun sendiri.');
        }

        if ($user->hasRole('admin') && $user->is_active && !$this->hasAnotherActiveAdmin($user)) {
            return back()->with('swal_error', 'Admin aktif terakhir tidak dapat dinonaktifkan.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('swal_success', "User berhasil {$status}.");
    }

    private function hasAnotherActiveAdmin(User $user): bool
    {
        return User::role('admin')
            ->where('is_active', true)
            ->whereKeyNot($user->id)
            ->exists();
    }
}
