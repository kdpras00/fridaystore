@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'Kelola User')
@section('page-sub', 'Daftar pengguna dan hak akses sistem POS.')
@section('page-actions')
<a href="{{ route('users.create') }}" class="btn btn-primary">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Tambah User
</a>
@endsection

@section('content')
<div class="page-intro">
    <div class="page-intro-copy">
        <div class="page-intro-title">Kelola User</div>
        <div class="page-intro-sub">Atur siapa yang boleh akses kasir, stok, laporan, dan pengelolaan data. Role yang jelas menjaga alur kerja tetap aman.</div>
    </div>
    <div class="page-intro-meta">
        <span class="info-chip">Total <strong>{{ number_format($users->total()) }}</strong></span>
        <span class="info-chip">Role <strong>Admin / Kasir / Owner</strong></span>
    </div>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th style="text-align:right;">Aksi</th>
            </tr></thead>
            <tbody>
            @forelse($users as $u)
            <tr>
                <td style="font-weight:500;color:var(--color-ink);">
                    {{ $u->name }}
                    @if($u->id === auth()->id()) <span style="font-size:12px;color:var(--color-ink-4);font-weight:400;margin-left:4px;">(Anda)</span> @endif
                </td>
                <td style="font-family:var(--font-mono);font-size:12.5px;color:var(--color-ink-3);">{{ $u->email }}</td>
                {{-- ponytail: plain text role instead of badge --}}
                <td style="font-size:13.5px;color:var(--color-ink-2);">{{ ucfirst($u->getRoleNames()->first() ?? '-') }}</td>
                <td>
                    <form id="toggle-{{ $u->id }}" method="POST" action="{{ route('users.toggle', $u) }}" style="display:none;">@csrf @method('PATCH')</form>
                    {{-- ponytail: plain link/text status toggle instead of badge --}}
                    <button onclick="confirmToggle('toggle-{{ $u->id }}','{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}')" style="cursor:pointer;border:none;background:none;padding:0;font-family:inherit;font-weight:400;color:var(--color-ink-2);font-size:13px;display:inline-flex;align-items:center;gap:6px;">
                        <span style="width:6px;height:6px;border-radius:50%;background:{{ $u->is_active ? 'var(--color-success)' : 'var(--color-danger)' }};"></span>
                        {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </td>
                <td style="text-align:right;">
                    <div style="display:flex;gap:8px;justify-content:flex-end;">
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-ghost btn-sm">Edit</a>
                        @if($u->id !== auth()->id())
                        <form id="del-usr-{{ $u->id }}" method="POST" action="{{ route('users.destroy', $u) }}" style="display:none;">@csrf @method('DELETE')</form>
                        <button onclick="confirmDelete('del-usr-{{ $u->id }}','{{ addslashes($u->name) }}')" class="btn btn-danger btn-sm" style="cursor:pointer;">Hapus</button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding:0;">
                    <div class="empty-state">
                        <div class="empty-state-title">Belum ada user</div>
                        <div class="empty-state-copy">Tambahkan user pertama untuk mulai membagi akses operasional.</div>
                        <div class="empty-state-actions">
                            <a href="{{ route('users.create') }}" class="btn btn-primary">Tambah User</a>
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--color-border-subtle);">{{ $users->links() }}</div>
    @endif
</div>
@endsection
