@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')

@section('content')

@php($avgTicket = $transaksi->total() ? $grandTotal / $transaksi->total() : 0)

<div class="page-intro">
    <div class="page-intro-copy">
        <div class="page-intro-title">Laporan Penjualan</div>
        <div class="page-intro-sub">Ringkasan transaksi harian hingga lintas kasir. Gunakan filter tanggal untuk membandingkan periode secara cepat.</div>
    </div>
    <div class="page-intro-meta">
        <span class="info-chip">Periode aktif <strong>{{ request('dari') || request('sampai') ? 'Custom' : 'All' }}</strong></span>
        <span class="info-chip">Rata-rata trx <strong>Rp {{ number_format($avgTicket, 0, ',', '.') }}</strong></span>
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

{{-- Filter --}}
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
        <div>
            <label class="form-label">Kasir</label>
            <select name="kasir_id" class="form-select" style="width:150px;">
                <option value="">Semua</option>
                @foreach($kasirList as $k)
                <option value="{{ $k->id }}" @selected(request('kasir_id')==$k->id)>{{ $k->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-ghost">Filter</button>
            <a href="{{ route('laporan.index') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>

{{-- Summary & Export --}}
<div class="metric-grid">
    <div class="metric-card">
        <div class="metric-label">Total Transaksi</div>
        <div class="metric-value">{{ number_format($transaksi->total()) }}</div>
        <div class="metric-note">Jumlah transaksi dalam filter aktif</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Total Pendapatan</div>
        <div class="metric-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
        <div class="metric-note">Pendapatan bersih dari transaksi terpilih</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Rata-rata Per Transaksi</div>
        <div class="metric-value">Rp {{ number_format($avgTicket, 0, ',', '.') }}</div>
        <div class="metric-note">Bantuan cepat untuk evaluasi ticket size</div>
    </div>
</div>

<div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
    <a href="{{ route('laporan.export', request()->query()) }}" class="btn btn-ghost" style="background:var(--color-surface); border:1px solid var(--color-border);">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="opacity:0.7;"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Export CSV
    </a>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th style="text-align:right;">Total Bayar</th>
            </tr></thead>
            <tbody>
            @forelse($transaksi as $t)
            <tr>
                <td style="font-family:var(--font-mono);font-size:13.5px;color:var(--color-amber);">{{ $t->no_invoice }}</td>
                <td style="font-size:13.5px;color:var(--color-ink-3);">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $t->kasir->name }}</td>
                <td style="text-align:right;font-family:var(--font-mono);font-weight:600;color:var(--color-ink);">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding:0;">
                    <div class="empty-state">
                        <div class="empty-state-title">Tidak ada data transaksi</div>
                        <div class="empty-state-copy">Sesuaikan rentang tanggal atau reset filter untuk melihat transaksi lain.</div>
                        <div class="empty-state-actions">
                            <a href="{{ route('laporan.index') }}" class="btn btn-ghost">Reset Filter</a>
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($transaksi->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--color-border-subtle);">{{ $transaksi->links() }}</div>
    @endif
</div>
@endsection
