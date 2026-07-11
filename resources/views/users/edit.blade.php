@extends('layouts.app')
@section('title', 'Edit User')
@section('content-class', 'form-page')
@section('hide-header', true)

@section('content')
<div class="page-intro" style="max-width:640px; width:100%;">
    <div class="page-intro-copy">
        <div class="page-intro-title">Edit User</div>
        <div class="page-intro-sub">Perubahan role atau status aktif akan langsung memengaruhi akses menu dan hak operasional pengguna.</div>
    </div>
</div>

<div class="form-card">
    <div class="form-title">Edit User — {{ $user->name }}</div>
    <form method="POST" action="{{ route('users.update', $user) }}" style="display:flex;flex-direction:column;gap:16px;">
        @csrf @method('PUT')
        <div>
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input {{ $errors->has('name') ? 'error' : '' }}" required>
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input {{ $errors->has('email') ? 'error' : '' }}" required>
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label class="form-label">
                    Password Baru
                    <span style="font-size:11px;color:var(--color-ink-4);font-weight:400;">(kosong = tidak ganti)</span>
                </label>
                <input type="password" name="password" class="form-input {{ $errors->has('password') ? 'error' : '' }}" placeholder="••••••••">
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
            </div>
        </div>
        <div>
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="admin" @selected(old('role', $user->getRoleNames()->first())=='admin')>Admin</option>
                <option value="kasir" @selected(old('role', $user->getRoleNames()->first())=='kasir')>Kasir</option>
                <option value="owner" @selected(old('role', $user->getRoleNames()->first())=='owner')>Owner</option>
            </select>
        </div>
        <div class="pos-scan-note" style="margin-top:0;">
            Pastikan role sesuai struktur kerja agar tidak ada menu yang terlalu banyak atau akses yang tidak diperlukan.
        </div>
            <div style="display:flex;gap:12px;margin-top:24px;justify-content:flex-end;">
                <a href="{{ route('users.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
    </form>
</div>
@endsection
