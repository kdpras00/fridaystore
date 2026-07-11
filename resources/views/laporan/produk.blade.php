@extends('layouts.app')
@section('title', 'Laporan Per Produk')
@section('page-title', 'Laporan Per Produk')

@section('content')

@php
    $topProduk = $produkLaris->first();
    $totalQty = $produkLaris->sum('total_qty');
    $totalRevenue = $produkLaris->sum('total_revenue');
@endphp

<div class="page-intro">
    <div class="page-intro-copy">
        <div class="page-intro-title">Laporan Per Produk</div>
        <div class="page-intro-sub">Lihat produk paling laku, kontribusi omzet, dan cek item yang perlu stok tambahan lebih cepat.</div>
    </div>
    <div class="page-intro-meta">
        <span class="info-chip">Item terjual <strong>{{ number_format($totalQty) }}</strong></span>
        <span class="info-chip">Omzet <strong>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong></span>
    </div>
</div>

{{-- Sub-tabs --}}
<div class="tab-list">
    <a href="{{ route('laporan.index') }}" class="tab-item {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
        Penjualan
    </a>
    <a href="{{ route('laporan.produk') }}" class="tab-item {{ request()->routeIs('laporan.produk') ? 'active' : '' }}">
        Per Produk
    </a>
    <a href="{{ route('laporan.kasir') }}" class="tab-item {{ request()->routeIs('laporan.kasir') ? 'active' : '' }}">
        Per Kasir
    </a>
</div>

{{-- Filter Tanggal --}}
<div class="card filter-card">
    <form method="GET" class="filter-bar">
        <div>
            <label class="form-label">Dari</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="form-input" style="width:160px;">
        </div>
        <div>
            <label class="form-label">Sampai</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-input" style="width:160px;">
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-ghost">Filter</button>
            <a href="{{ route('laporan.produk') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>

<div class="metric-grid">
    <div class="metric-card">
        <div class="metric-label">Top Produk</div>
        <div class="metric-value">{{ $topProduk?->nama_produk ?? '-' }}</div>
        <div class="metric-note">{{ $topProduk ? number_format($topProduk->total_qty) . ' unit terjual' : 'Belum ada data' }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Item Terjual</div>
        <div class="metric-value">{{ number_format($totalQty) }}</div>
        <div class="metric-note">Seluruh unit pada periode aktif</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Omzet Produk</div>
        <div class="metric-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="metric-note">Kontribusi pendapatan tiap produk</div>
    </div>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Nama Produk</th>
                    <th>Terjual</th>
                    <th style="text-align:right;">Total Revenue</th>
                </tr>
            </thead>
            <tbody>
            @forelse($produkLaris as $i => $p)
            <tr>
                <td style="color:var(--color-ink-4);">{{ $i + 1 }}</td>
                <td class="td-primary">{{ $p->nama_produk }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="height:6px;border-radius:3px;background:var(--color-amber-glow);width:{{ min(100, ($p->total_qty / ($produkLaris->first()->total_qty ?: 1)) * 100) }}%;max-width:80px;"></div>
                        <span style="font-family:var(--font-mono);font-size:13.5px;font-weight:600;color:var(--color-ink-2);">{{ number_format($p->total_qty) }}</span>
                    </div>
                </td>
                <td style="text-align:right;font-family:var(--font-mono);font-size:14px;font-weight:600;color:var(--color-amber);">
                    Rp {{ number_format($p->total_revenue, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding:0;">
                    <div class="empty-state">
                        <div class="empty-state-title">Tidak ada data produk</div>
                        <div class="empty-state-copy">Coba ubah rentang tanggal untuk melihat performa produk yang lain.</div>
                        <div class="empty-state-actions">
                            <a href="{{ route('laporan.produk') }}" class="btn btn-ghost">Reset Filter</a>
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($produkLaris->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--color-border-subtle);">{{ $produkLaris->links() }}</div>
    @endif
</div>
@endsection
