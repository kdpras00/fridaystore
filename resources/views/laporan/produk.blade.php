@extends('layouts.app')
@section('title', 'Laporan Per Produk')
@section('page-title', 'Laporan Per Produk')
@section('page-actions')
<button type="button" id="btn-export-csv" class="btn btn-ghost">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
    Export CSV
</button>
@endsection

@section('content')

<div class="tab-list">
    <a href="{{ route('laporan.index') }}" class="tab-item {{ request()->routeIs('laporan.index') ? 'active' : '' }}">Penjualan</a>
    <a href="{{ route('laporan.produk') }}" class="tab-item {{ request()->routeIs('laporan.produk') ? 'active' : '' }}">Per Produk</a>
</div>

<div class="card filter-card">
    <form method="GET" class="filter-bar">
        <div>
            <label class="form-label">Dari</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input" style="width:160px;">
        </div>
        <div>
            <label class="form-label">Sampai</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input" style="width:160px;">
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
        <div class="metric-value" style="font-size:15px;letter-spacing:-0.01em;">{{ $topProduk?->nama_produk ?? '—' }}</div>
        <div class="metric-note">{{ $topProduk ? number_format($topProduk->total_qty).' unit terjual' : 'Belum ada data' }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Total Unit Terjual</div>
        <div class="metric-value">{{ number_format($grandTotalQty) }}</div>
        <div class="metric-note">Seluruh unit pada periode aktif</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Item Tersedia</div>
        <div class="metric-value">{{ $produkLaris->count() }}</div>
        <div class="metric-note">Macam produk terjual</div>
    </div>
</div>

<div class="card table-card">
    <div style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--color-border); flex-wrap:wrap; gap:12px;">
        <h3 style="font-size:15px; font-weight:600; color:var(--color-ink); margin:0;">Rincian Penjualan</h3>
        <div style="display:flex; gap:8px; align-items:center;">
            <span style="font-size:12.5px; color:var(--color-ink-3);">Urutkan:</span>
            <button type="button" id="btn-sort-desc" class="btn btn-ghost btn-sm" style="font-size:12px; padding:4px 10px;">Paling Laku</button>
            <button type="button" id="btn-sort-asc" class="btn btn-ghost btn-sm" style="font-size:12px; padding:4px 10px;">Kurang Laku</button>
        </div>
    </div>
    <div class="table-wrap">
        <table id="tbl-produk-laporan" class="data-table" style="width:100%">
            <thead><tr>
                <th style="width:44px;">#</th>
                <th>Nama Produk</th>
                <th style="text-align:right;">Unit Terjual</th>
                <th style="text-align:right;">Stok Tersisa</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($produkLaris as $i => $p)
            <tr>
                <td style="color:var(--color-ink-4);font-family:var(--font-mono);">{{ $i + 1 }}</td>
                <td style="font-weight:500;color:var(--color-ink);">{{ $p->nama_produk }}</td>
                <td style="text-align:right;font-family:var(--font-mono);font-weight:600;color:var(--color-ink-2);"
                    data-order="{{ $p->total_qty }}">
                    {{ number_format($p->total_qty) }}
                </td>
                <td style="text-align:right;font-family:var(--font-mono);font-weight:600;color:{{ ($p->produk->stok ?? 0) <= 5 ? 'var(--color-danger)' : 'var(--color-ink-3)' }};"
                    data-order="{{ $p->produk->stok ?? 0 }}">
                    {{ number_format($p->produk->stok ?? 0) }}
                </td>
                <td></td>
            </tr>
            @empty
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.dt-init', [
    'tableId' => 'tbl-produk-laporan',
    'config'  => "{
        order: [[2, 'desc']],
        columnDefs: [
            { className: 'dtr-control', orderable: false, targets: -1 },
            { orderable: false, targets: [0] },
            { responsivePriority: 1, targets: 1 },
            { responsivePriority: 2, targets: 2 },
            { responsivePriority: 3, targets: 3 },
            { responsivePriority: 10, targets: [0] },
        ],
        buttons: window.DT_EXPORT_BUTTONS,
        language: { emptyTable: '<div style=\'text-align: center;\'>Tidak ada data pada periode ini</div>' },
    }",
    'extra' => "
        var btnCsv = document.getElementById('btn-export-csv');
        if (btnCsv) btnCsv.onclick = function() { dt.button(0).trigger(); };
        
        var btnDesc = document.getElementById('btn-sort-desc');
        if (btnDesc) btnDesc.onclick = function() { dt.order([2, 'desc']).draw(); };
        
        var btnAsc = document.getElementById('btn-sort-asc');
        if (btnAsc) btnAsc.onclick = function() { dt.order([2, 'asc']).draw(); };
    ",
])
@endpush
