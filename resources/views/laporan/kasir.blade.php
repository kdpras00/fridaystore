@extends('layouts.app')
@section('title', 'Laporan Per Kasir')
@section('page-title', 'Laporan Per Kasir')

@section('content')

@php($totalKasir = $kasirStats->count())

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
            <a href="{{ route('laporan.kasir') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>

<div class="metric-grid">
    @forelse($kasirStats as $k)
    <div class="metric-card">
        <div class="metric-label">{{ $k->name }}</div>
        <div class="metric-value">Rp {{ number_format($k->total_omzet ?? 0, 0, ',', '.') }}</div>
        <div class="metric-note">{{ number_format($k->total_transaksi) }} transaksi</div>
    </div>
    @empty
    <div style="grid-column:1/-1;">
        <div class="empty-state">
            <div class="empty-state-title">Tidak ada data kasir.</div>
            <div class="empty-state-copy">Periksa kembali filter tanggal yang dipilih.</div>
        </div>
    </div>
    @endforelse
</div>
@endsection
