@extends('layouts.app')
@section('title', 'Tambah User')
@section('content-class', 'form-page')
@section('hide-header', true)

@section('content')
<div class="page-intro" style="max-width:640px; width:100%;">
    <div class="page-intro-copy">
        <div class="page-intro-title">Tambah User</div>
        <div class="page-intro-sub">Atur akses sesuai role agar kasir, admin, dan owner hanya melihat menu yang relevan dengan tugas mereka.</div>
    </div>
</div>

<div class="form-card">
    <div class="form-title">Tambah User Baru</div>
    <form method="POST" action="{{ route('users.store') }}" style="display:flex;flex-direction:column;gap:16px;">
        @csrf
        <div>
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-input {{ $errors->has('name') ? 'error' : '' }}" placeholder="cth: Budi Santoso" required>
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-input {{ $errors->has('email') ? 'error' : '' }}" placeholder="budi@toko.com" required>
            @error('email') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input {{ $errors->has('password') ? 'error' : '' }}" placeholder="••••••••" required>
                @error('password') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required>
            </div>
        </div>
        <div>
            <label class="form-label">Role</label>
            <select name="role" class="form-select {{ $errors->has('role') ? 'error' : '' }}" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin" @selected(old('role')=='admin')>Admin</option>
                <option value="kasir" @selected(old('role')=='kasir')>Kasir</option>
                <option value="owner" @selected(old('role')=='owner')>Owner</option>
            </select>
            @error('role') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="pos-scan-note" style="margin-top:0;">
            Role menentukan menu yang tampil di sidebar. Untuk operasional kasir, minimalisasi akses agar workflow tetap cepat dan aman.
        </div>
        <div style="display:flex;gap:12px;margin-top:24px;justify-content:flex-end;">
            <a href="{{ route('users.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan User</button>
        </div>
    </form>
</div>
@endsection
