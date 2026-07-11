@extends('layouts.app')
@section('title', 'Produk')
@section('page-title', 'Kelola Produk')
@section('page-sub', 'Daftar produk, harga jual, dan stok barang toko.')
@section('page-actions')
<a href="{{ route('produk.create') }}" class="btn btn-primary">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
    Tambah Produk
</a>
@endsection

@section('content')

<div class="page-intro">
    <div class="page-intro-copy">
        <div class="page-intro-title">Kelola Produk</div>
        <div class="page-intro-sub">Inventaris utama untuk transaksi kasir, stok, dan laporan. Jaga kode produk tetap singkat, konsisten, dan mudah dicari.</div>
    </div>
    <div class="page-intro-meta">
        <span class="info-chip">Total <strong>{{ number_format($produk->total()) }}</strong></span>
        <span class="info-chip">Kategori <strong>{{ number_format($kategori->count()) }}</strong></span>
    </div>
</div>

<div class="card filter-card">
    <form method="GET" class="filter-bar">
        <div>
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / kode…" class="form-input" style="width:220px;">
        </div>
        <div>
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-select" style="width:auto;">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $k)
                <option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-ghost">Filter</button>
            @if(request()->hasAny(['search','kategori_id']))
            <a href="{{ route('produk.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr>
                <th>Gambar</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                {{-- ponytail: plain text category instead of badge --}}
                <th>Kategori</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th style="text-align:right;">Aksi</th>
            </tr></thead>
            <tbody>
            @forelse($produk as $p)
            <tr>
                <td>
                    <div style="width:48px;height:48px;border-radius:8px;background:var(--color-surface-3);border:1px solid var(--color-border-subtle);display:flex;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">
                        @if($p->gambar)
                        <img src="{{ asset('storage/'.$p->gambar) }}" style="width:100%;height:100%;object-fit:cover;" alt="">
                        @else
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="var(--color-ink-4)" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        @endif
                    </div>
                </td>
                <td style="font-size:12.5px;color:var(--color-ink-3);font-family:var(--font-mono);">{{ $p->kode_produk }}</td>
                <td style="font-weight:500;color:var(--color-ink);">{{ $p->nama }}</td>
                {{-- ponytail: plain text category instead of badge --}}
                <td style="font-size:13.5px;color:var(--color-ink-2);">{{ $p->kategori->nama }}</td>
                <td style="font-family:var(--font-mono);font-size:12.5px;font-weight:500;color:var(--color-ink);">
                    Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
                </td>
                <td>
                    <span style="font-size:13px;color:var(--color-ink-2);display:inline-flex;align-items:center;gap:6px;">
                        @if($p->isStokRendah())
                            <span title="Stok Rendah" style="width:6px;height:6px;border-radius:50%;background:var(--color-danger);"></span>
                        @endif
                        {{ $p->stok }}
                    </span>
                </td>
                <td style="text-align:right;">
                    <div style="display:flex;gap:8px;justify-content:flex-end;">
                        <a href="{{ route('produk.edit', $p) }}" class="btn btn-ghost btn-sm">Edit</a>
                        <form id="del-prd-{{ $p->id }}" method="POST" action="{{ route('produk.destroy', $p) }}" style="display:none;">@csrf @method('DELETE')</form>
                        <button onclick="confirmDelete('del-prd-{{ $p->id }}','{{ addslashes($p->nama) }}')" class="btn btn-danger btn-sm" style="cursor:pointer;">Hapus</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding:0;">
                    <div class="empty-state">
                        <div class="empty-state-title">Tidak ada produk ditemukan</div>
                        <div class="empty-state-copy">Coba hapus filter atau buat produk baru untuk mulai mengisi katalog.</div>
                        <div class="empty-state-actions">
                            <a href="{{ route('produk.create') }}" class="btn btn-primary">Tambah Produk</a>
                            @if(request()->hasAny(['search','kategori_id']))
                            <a href="{{ route('produk.index') }}" class="btn btn-ghost">Reset Filter</a>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($produk->hasPages())
    <div style="padding:12px 16px; border-top:1px solid var(--color-border-subtle);">{{ $produk->links() }}</div>
    @endif
</div>
@endsection
