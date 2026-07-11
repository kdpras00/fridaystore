@extends('layouts.app')
@section('title', 'Lihat Stok')
@section('page-title', 'Lihat Stok Barang')

@section('content')
<div class="card filter-card">
    <form method="GET" class="filter-bar">
        <div>
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama produk…" class="form-input" style="width:220px;">
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-ghost">Cari</button>
            @if(request('search')) <a href="{{ route('owner.stok') }}" class="btn btn-ghost">Reset</a> @endif
        </div>
    </form>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th style="text-align:right;">Stok</th>
                    <th style="text-align:right;">Min.</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($produk as $p)
            <tr>
                <td>
                    <p style="font-size:13px;font-weight:500;color:var(--color-ink);">{{ $p->nama }}</p>
                    <p style="font-size:10.5px;color:var(--color-ink-4);font-family:var(--font-mono);">{{ $p->kode_produk }}</p>
                </td>
                <td style="font-size:13.5px;color:var(--color-ink-2);">{{ $p->kategori->nama }}</td>
                <td style="text-align:right;font-family:var(--font-mono);font-size:13px;font-weight:400;color:var(--color-ink-2);">
                    {{ $p->stok }}
                </td>
                <td style="text-align:right;font-size:12px;color:var(--color-ink-4);">{{ $p->stok_minimum }}</td>
                <td>
                    <span style="font-size:13px;font-weight:400;color:var(--color-ink-2);display:inline-flex;align-items:center;gap:6px;">
                        <span style="width:6px;height:6px;border-radius:50%;background:{{ $p->isStokRendah() ? 'var(--color-danger)' : 'var(--color-success)' }};"></span>
                        {{ $p->isStokRendah() ? 'Rendah' : 'Aman' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding:0;">
                    <div class="empty-state">
                        <div class="empty-state-title">Tidak ada data</div>
                        <div class="empty-state-copy">Tidak ada produk yang cocok dengan pencarian saat ini.</div>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($produk->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--color-border-subtle);">{{ $produk->links() }}</div>
    @endif
</div>
@endsection
