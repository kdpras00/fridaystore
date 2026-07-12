@extends('layouts.app')
@section('title', 'Riwayat Mutasi Stok')
@section('page-title', 'Riwayat Mutasi Stok')
@section('page-actions')
<a href="{{ route('stok.index') }}" class="btn btn-ghost">← Kembali</a>
@endsection

@section('content')

{{-- Quick filters (fed into DT column search) --}}
<div class="card filter-card" style="margin-bottom:16px;">
    <div class="filter-bar" style="align-items:flex-end;">
        <div>
            <label class="form-label" for="filter-produk">Produk</label>
            <select id="filter-produk" class="form-select" style="width:220px;" onchange="dtRiwayat.column(1).search(this.value,false,false).draw()">
                <option value="">Semua Produk</option>
                @foreach($produkList as $p)
                <option value="{{ $p->nama }}">{{ $p->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="filter-tipe">Tipe</label>
            <select id="filter-tipe" class="form-select" style="width:130px;" onchange="dtRiwayat.column(2).search(this.value,false,false).draw()">
                <option value="">Semua Tipe</option>
                <option value="Masuk">Masuk</option>
                <option value="Keluar">Keluar</option>
            </select>
        </div>
        <button type="button" class="btn btn-ghost" onclick="window.resetRiwayatFilter && window.resetRiwayatFilter()">Reset</button>
    </div>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table id="tbl-riwayat" class="data-table" style="width:100%">
            <thead><tr>
                <th>Waktu</th>
                <th>Produk</th>
                <th>Tipe</th>
                <th style="text-align:right;">Jumlah</th>
                <th>Keterangan</th>
                <th>Oleh</th>
                <th></th>
            </tr></thead>
            <tbody>
            @foreach($mutasi as $m)
            <tr>
                <td style="font-family:var(--font-mono);font-size:12px;color:var(--color-ink-3);white-space:nowrap;"
                    data-order="{{ $m->created_at->timestamp }}">
                    {{ $m->created_at->format('d/m/Y H:i') }}
                </td>
                <td style="font-weight:500;color:var(--color-ink);">{{ $m->produk->nama }}</td>
                <td data-order="{{ $m->tipe === 'masuk' ? 1 : 0 }}">
                    <span style="font-size:13px;font-weight:600;
                                 color:{{ $m->tipe === 'masuk' ? 'var(--color-success)' : 'var(--color-danger)' }};">
                        {{ $m->tipe === 'masuk' ? '↑ Masuk' : '↓ Keluar' }}
                    </span>
                </td>
                <td style="text-align:right;font-family:var(--font-mono);font-weight:600;color:var(--color-ink);"
                    data-order="{{ $m->jumlah }}">{{ $m->jumlah }}</td>
                <td style="color:var(--color-ink-3);">{{ $m->keterangan ?? '—' }}</td>
                <td style="font-size:12px;color:var(--color-ink-3);">{{ $m->user->name }}</td>
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
    'tableId' => 'tbl-riwayat',
    'config'  => "{
        order: [[0, 'desc']],
        columnDefs: [
            { className: 'dtr-control', orderable: false, targets: -1 },
            { responsivePriority: 1, targets: 1 },
            { responsivePriority: 2, targets: 2 },
            { responsivePriority: 3, targets: 3 },
            { responsivePriority: 10, targets: [0, 4, 5] },
        ],
        buttons: window.DT_EXPORT_BUTTONS,
    }",
    'extra' => "
        // Wire the quick-filter dropdowns to DT column search
        var produkSel = document.getElementById('filter-produk');
        var tipeSel   = document.getElementById('filter-tipe');
        if (produkSel) produkSel.onchange = function() { dt.column(1).search(this.value,false,false).draw(); };
        if (tipeSel)   tipeSel.onchange   = function() { dt.column(2).search(this.value,false,false).draw(); };
        window.resetRiwayatFilter = function() {
            if (produkSel) produkSel.value = '';
            if (tipeSel)   tipeSel.value   = '';
            dt.columns([1,2]).search('').draw();
        };
    ",
])
@endpush
