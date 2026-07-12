@extends('layouts.app')
@section('title', 'Users')
@section('page-title', 'Kelola User')
@section('page-sub', 'Daftar pengguna dan hak akses sistem POS.')
@section('page-actions')
<a href="{{ route('users.create') }}" class="btn btn-primary">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
    </svg>
    Tambah User
</a>
@endsection

@section('content')
<div class="card table-card">
    <div class="table-wrap">
        <table id="tbl-users" class="data-table" style="width:100%">
            <thead><tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th class="dt-no-export" style="text-align:right;">Aksi</th>
                <th></th>
            </tr></thead>
            <tbody>
            @foreach($users as $u)
            <tr>
                <td style="font-weight:500;color:var(--color-ink);">
                    {{ $u->name }}
                    @if($u->id === auth()->id())
                    <span style="font-size:12px;color:var(--color-ink-4);font-weight:400;margin-left:4px;">(Anda)</span>
                    @endif
                </td>
                <td style="font-family:var(--font-mono);font-size:12.5px;color:var(--color-ink-3);">{{ $u->email }}</td>
                <td style="font-size:13.5px;color:var(--color-ink-2);">{{ ucfirst($u->getRoleNames()->first() ?? '-') }}</td>
                <td data-order="{{ $u->is_active ? 1 : 0 }}">
                    <form id="toggle-{{ $u->id }}" method="POST" action="{{ route('users.toggle', $u) }}" style="display:none;">@csrf @method('PATCH')</form>
                    <button onclick="confirmToggle('toggle-{{ $u->id }}','{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}')"
                            style="cursor:pointer;border:none;background:none;padding:0;font-family:inherit;
                                   font-weight:400;color:var(--color-ink-2);font-size:13px;
                                   display:inline-flex;align-items:center;gap:6px;">
                        <span style="width:6px;height:6px;border-radius:50%;
                                     background:{{ $u->is_active ? 'var(--color-success)' : 'var(--color-danger)' }};"></span>
                        {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </td>
                <td class="dt-no-export" style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('users.edit', $u) }}" class="btn btn-ghost btn-sm">Edit</a>
                    @if($u->id !== auth()->id())
                    <form id="del-usr-{{ $u->id }}" method="POST" action="{{ route('users.destroy', $u) }}" style="display:none;">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-usr-{{ $u->id }}','{{ addslashes($u->name) }}')" class="btn btn-danger btn-sm" style="cursor:pointer;">Hapus</button>
                    @endif
                </td>
                <td></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.dt-init', [
    'tableId' => 'tbl-users',
    'config'  => "{
        order: [[0, 'asc']],
        columnDefs: [
            { className: 'dtr-control', orderable: false, targets: -1 },
            { orderable: false, targets: [4] },
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 3 },
            { responsivePriority: 10, targets: [1, 2, 4] },
        ],
    }",
])
@endpush
