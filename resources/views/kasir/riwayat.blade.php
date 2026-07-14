@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('page-title', 'Riwayat Transaksi Saya')
@section('page-sub', 'Semua transaksi yang sudah Anda proses.')
@section('page-actions')
<button type="button" id="btn-export-csv" class="btn btn-ghost">
    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
    Export CSV
</button>
@endsection

@section('content')

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
            <a href="{{ route('kasir.riwayat') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>

<div class="metric-grid" style="margin-bottom:20px;">
    <div class="metric-card">
        <div class="metric-label">Total Transaksi</div>
        <div class="metric-value">{{ number_format(count($transaksi)) }}</div>
        <div class="metric-note">Transaksi dalam filter aktif</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Total Omzet</div>
        <div class="metric-value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
        <div class="metric-note">Pendapatan dari transaksi Anda</div>
    </div>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table id="tbl-riwayat-kasir" class="data-table" style="width:100%">
            <thead><tr>
                <th>Invoice</th>
                <th>Tanggal</th>
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
                <td style="text-align:right;font-family:var(--font-mono);font-size:13px;color:var(--color-ink-3);"
                    data-order="{{ $t->total_harga }}">
                    Rp {{ number_format($t->total_harga, 0, ',', '.') }}
                </td>
                <td style="text-align:right;font-family:var(--font-mono);font-size:13px;
                           color:{{ $t->diskon > 0 ? 'var(--color-danger)' : 'var(--color-ink-4)' }};"
                    data-order="{{ $t->diskon }}">
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
    'tableId' => 'tbl-riwayat-kasir',
    'config'  => "{
        order: [[1, 'desc']],
        columnDefs: [
            { className: 'dtr-control', orderable: false, targets: -1 },
            { orderable: false, targets: [5] },
            { responsivePriority: 1, targets: 0 },
            { responsivePriority: 2, targets: 4 },
            { responsivePriority: 3, targets: 1 },
            { responsivePriority: 10, targets: [2, 3, 5] },
        ],
        buttons: window.DT_EXPORT_BUTTONS,
        language: { emptyTable: '<div style=\'text-align: center;\'>Belum ada transaksi pada periode ini</div>' },
    }",
    'extra' => "
        var btnCsv = document.getElementById('btn-export-csv');
        if (btnCsv) btnCsv.onclick = function() { dt.button(0).trigger(); };
    ",
])
@endpush
