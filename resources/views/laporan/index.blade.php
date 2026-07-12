@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')
@section('page-actions')
<button type="button" id="btn-export-csv" class="btn btn-ghost">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
    Export CSV
</button>
<button type="button" id="btn-print" class="btn btn-ghost">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a1 1 0 001-1v-4a1 1 0 00-1-1H9a1 1 0 00-1 1v4a1 1 0 001 1zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
    Print
</button>
@endsection

@section('content')

@php($avgTicket = count($transaksi) ? $grandTotal / count($transaksi) : 0)

<div class="tab-list">
    <a href="{{ route('laporan.index') }}" class="tab-item {{ request()->routeIs('laporan.index') ? 'active' : '' }}">Penjualan</a>
    <a href="{{ route('laporan.produk') }}" class="tab-item {{ request()->routeIs('laporan.produk') ? 'active' : '' }}">Per Produk</a>
    <a href="{{ route('laporan.kasir') }}" class="tab-item {{ request()->routeIs('laporan.kasir') ? 'active' : '' }}">Per Kasir</a>
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
        <div>
            <label class="form-label">Kasir</label>
            <select name="kasir_id" class="form-select" style="width:150px;">
                <option value="">Semua</option>
                @foreach($kasirList as $k)
                <option value="{{ $k->id }}" @selected(request('kasir_id') == $k->id)>{{ $k->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-ghost">Filter</button>
            <a href="{{ route('laporan.index') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>

<div class="metric-grid">
    <div class="metric-card">
        <div class="metric-label">Total Transaksi</div>
        <div class="metric-value">{{ number_format(count($transaksi)) }}</div>
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
        <div class="metric-note">Ticket size periode ini</div>
    </div>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table id="tbl-laporan" class="data-table" style="width:100%">
            <thead><tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Kasir</th>
                <th style="text-align:right;">Subtotal</th>
                <th style="text-align:right;">Diskon</th>
                <th style="text-align:right;">Total Bayar</th>
                <th class="dt-no-export" style="text-align:right;">Aksi</th>
                <th></th>
            </tr></thead>
            <tbody>
            @forelse($transaksi as $t)
            <tr>
                <td style="font-family:var(--font-mono);font-size:13px;color:var(--color-amber);">{{ $t->no_invoice }}</td>
                <td style="font-size:13px;color:var(--color-ink-3);" data-order="{{ $t->created_at->timestamp }}">
                    {{ $t->created_at->format('d/m/Y H:i') }}
                </td>
                <td>{{ $t->kasir->name }}</td>
                <td style="text-align:right;font-family:var(--font-mono);font-size:13px;color:var(--color-ink-3);"
                    data-order="{{ $t->total_harga }}">
                    Rp {{ number_format($t->total_harga, 0, ',', '.') }}
                </td>
                <td style="text-align:right;font-family:var(--font-mono);font-size:13px;"
                    data-order="{{ $t->diskon }}"
                    style="color:{{ $t->diskon > 0 ? 'var(--color-danger)' : 'var(--color-ink-4)' }}">
                    {{ $t->diskon > 0 ? '- Rp '.number_format($t->diskon, 0, ',', '.') : '—' }}
                </td>
                <td style="text-align:right;font-family:var(--font-mono);font-weight:600;color:var(--color-ink);"
                    data-order="{{ $t->total_bayar }}">
                    Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                </td>
                <td class="dt-no-export" style="text-align:right;">
                    <a href="{{ route('kasir.struk', $t) }}" target="_blank" class="btn btn-ghost btn-sm">Struk</a>
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
    'tableId' => 'tbl-laporan',
    'config'  => "{
        order: [[1, 'desc']],
        columnDefs: [
            { className: 'dtr-control', orderable: false, targets: -1 },
            { orderable: false, targets: [6] },
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 5 },
            { responsivePriority: 3, targets: 1 },
            { responsivePriority: 10, targets: [2, 3, 4, 6] },
        ],
        buttons: window.DT_EXPORT_BUTTONS,
        language: { emptyTable: 'Tidak ada transaksi pada periode ini' },
    }",
    'extra' => "
        var btnCsv   = document.getElementById('btn-export-csv');
        var btnPrint = document.getElementById('btn-print');
        if (btnCsv)   btnCsv.onclick   = function() { dt.button(0).trigger(); };
        if (btnPrint) btnPrint.onclick = function() { dt.button(1).trigger(); };
    ",
])
@endpush
