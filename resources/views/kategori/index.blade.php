@extends('layouts.app')
@section('title', 'Kategori Produk')
@section('page-title', 'Kategori Produk')

@section('content')
<div class="page-intro">
    <div class="page-intro-copy">
        <div class="page-intro-title">Kategori Produk</div>
        <div class="page-intro-sub">Gunakan kategori untuk menjaga katalog tetap rapi dan mempermudah filter di produk, stok, dan laporan.</div>
    </div>
    <div class="page-intro-meta">
        <span class="info-chip">Total <strong>{{ number_format($kategori->count()) }}</strong></span>
        <span class="info-chip">Pengelompokan <strong>Aktif</strong></span>
    </div>
</div>

<div class="category-layout" style="display:grid; grid-template-columns:280px 1fr; gap:16px; align-items:start;">

    {{-- Form panel --}}
    <div class="card">
        <p class="card-title" style="margin-bottom:14px;">Tambah Kategori</p>
        <form method="POST" action="{{ route('kategori.store') }}" style="display:flex;flex-direction:column;gap:12px;">
            @csrf
            <div>
                <label class="form-label" for="kategori-nama">Nama Kategori</label>
                <input id="kategori-nama" type="text" name="nama" value="{{ old('nama') }}" class="form-input {{ $errors->has('nama') ? 'error' : '' }}" placeholder="cth: Jaket" required>
                @error('nama') <p class="form-error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="justify-content:center;">Simpan</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="card table-card">
        <div class="card-header">
            <p class="card-title">Semua Kategori</p>
            <span style="font-size:11px; color:var(--color-ink-4);">{{ $kategori->count() }} kategori</span>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr>
                    <th>Nama</th>
                    <th>Produk</th>
                    <th style="text-align:right;">Aksi</th>
                </tr></thead>
                <tbody>
                @forelse($kategori as $k)
                <tr>
                    <td class="td-primary">{{ $k->nama }}</td>
                    <td>
                        <span style="font-size:13.5px;color:var(--color-ink-2);">{{ $k->produk_count }} item</span>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex; gap:8px; justify-content:flex-end;">
                            <button onclick="openEdit({{ $k->id }},'{{ addslashes($k->nama) }}')" class="btn btn-ghost btn-sm" style="cursor:pointer;">Edit</button>
                            <form id="del-kat-{{ $k->id }}" method="POST" action="{{ route('kategori.destroy', $k) }}" style="display:none;">@csrf @method('DELETE')</form>
                            <button onclick="confirmDelete('del-kat-{{ $k->id }}','{{ addslashes($k->nama) }}')" class="btn btn-danger btn-sm" style="cursor:pointer;">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding:0;">
                        <div class="empty-state">
                            <div class="empty-state-title">Belum ada kategori</div>
                            <div class="empty-state-copy">Buat kategori pertama agar katalog produk lebih mudah dikelola.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit modal --}}
<div id="edit-modal" style="display:none;" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="edit-modal-title">
    <div class="modal-box">
        <p id="edit-modal-title" class="modal-title">Edit Kategori</p>
        <p class="modal-sub">Ubah nama kategori produk</p>
        <form id="edit-form" method="POST" style="display:flex;flex-direction:column;gap:12px;">
            @csrf @method('PUT')
            <div>
                <label class="form-label" for="edit-nama">Nama</label>
                <input type="text" id="edit-nama" name="nama" class="form-input" required>
            </div>
            <div style="display:flex;gap:10px;margin-top:12px;justify-content:flex-end;">
                <button type="button" onclick="closeEdit()" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEdit(id, nama) {
    document.getElementById('edit-form').action = '/kategori/' + id;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-modal').style.display = 'flex';
    setTimeout(() => document.getElementById('edit-nama').focus(), 0);
}
function closeEdit() { document.getElementById('edit-modal').style.display = 'none'; }
</script>
@endpush
