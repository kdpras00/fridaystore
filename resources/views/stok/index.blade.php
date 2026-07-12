@extends('layouts.app')
@section('title', 'Stok')
@section('page-title', 'Stok Barang')
@section('page-sub', 'Kelola stok barang dan riwayat mutasi masuk/keluar.')
@section('page-actions')
<a href="{{ route('stok.riwayat') }}" class="btn btn-ghost">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    Riwayat
</a>
@endsection

@section('content')

@if($lowStockCount > 0)
<div style="display:flex;align-items:center;gap:10px;padding:10px 14px;margin-bottom:16px;
            background:var(--color-danger-dim);border:1px solid var(--color-danger);
            border-radius:8px;opacity:0.9;">
    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--color-danger)" stroke-width="2" style="flex-shrink:0;">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
    </svg>
    <span style="font-size:13px;color:var(--color-danger);font-weight:500;">
        {{ $lowStockCount }} produk stok rendah.
        <a href="#" onclick="filterStatus('rendah');return false;"
           style="font-weight:600;text-decoration:underline;color:var(--color-danger);">Lihat sekarang</a>
    </span>
</div>
@endif

{{-- Status quick-filter --}}
<div class="card filter-card" style="margin-bottom:16px;">
    <div class="filter-bar" style="align-items:flex-end;">
        <div>
            <label class="form-label" for="filter-status">Status Stok</label>
            <select id="filter-status" class="form-select" style="width:160px;" onchange="filterStatus(this.value)">
                <option value="">Semua</option>
                <option value="Rendah">Stok Rendah</option>
                <option value="Aman">Stok Aman</option>
            </select>
        </div>
        <button type="button" onclick="filterStatus('')" class="btn btn-ghost">Reset</button>
    </div>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table id="tbl-stok" class="data-table" style="width:100%">
            <thead><tr>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th style="text-align:right;">Stok</th>
                <th style="text-align:right;">Min.</th>
                <th>Status</th>
                <th class="dt-no-export" style="text-align:right;">Aksi</th>
                <th></th>
            </tr></thead>
            <tbody>
            @foreach($produk as $p)
            <tr>
                <td style="font-size:12.5px;color:var(--color-ink-3);font-family:var(--font-mono);">{{ $p->kode_produk }}</td>
                <td style="font-weight:500;color:var(--color-ink);">{{ $p->nama }}</td>
                <td style="color:var(--color-ink-2);">{{ $p->kategori->nama }}</td>
                <td style="text-align:right;font-family:var(--font-mono);font-size:13px;color:var(--color-ink-2);"
                    data-order="{{ $p->stok }}">{{ $p->stok }}</td>
                <td style="text-align:right;font-size:12px;color:var(--color-ink-4);"
                    data-order="{{ $p->stok_minimum }}">{{ $p->stok_minimum }}</td>
                <td data-order="{{ $p->isStokRendah() ? 0 : 1 }}">
                    <span style="font-size:13px;color:var(--color-ink-2);display:inline-flex;align-items:center;gap:6px;">
                        <span style="width:6px;height:6px;border-radius:50%;flex-shrink:0;
                                     background:{{ $p->isStokRendah() ? 'var(--color-danger)' : 'var(--color-success)' }};"></span>
                        {{ $p->isStokRendah() ? 'Rendah' : 'Aman' }}
                    </span>
                </td>
                <td class="dt-no-export" style="text-align:right;">
                    <button onclick="openRestock({{ $p->id }},'{{ addslashes($p->nama) }}')"
                            class="btn btn-success btn-sm" style="cursor:pointer;">+ Restock</button>
                </td>
                <td></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Restock modal --}}
<div id="restock-modal" style="display:none;" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="restock-modal-title">
    <div class="modal-box">
        <p id="restock-modal-title" class="modal-title">Restock Produk</p>
        <p id="restock-nama" class="modal-sub"></p>
        <form method="POST" action="{{ route('stok.restock') }}" style="display:flex;flex-direction:column;gap:12px;">
            @csrf
            <input type="hidden" id="restock-id" name="produk_id">
            <div>
                <label class="form-label" for="restock-jumlah">Jumlah Tambahan</label>
                <input id="restock-jumlah" type="number" name="jumlah" class="form-input" min="1" required placeholder="50">
            </div>
            <div>
                <label class="form-label" for="restock-keterangan">
                    Keterangan <span style="color:var(--color-ink-4);">(opsional)</span>
                </label>
                <input id="restock-keterangan" type="text" name="keterangan" class="form-input" placeholder="cth: Pembelian supplier">
            </div>
            <div style="display:flex;gap:10px;margin-top:12px;justify-content:flex-end;">
                <button type="button" onclick="closeRestock()" class="btn btn-ghost">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.dt-init', [
    'tableId' => 'tbl-stok',
    'config'  => "{
        order: [[1, 'asc']],
        columnDefs: [
            { className: 'dtr-control', orderable: false, targets: -1 },
            { orderable: false, targets: [6] },
            { responsivePriority: 1, targets: 1 },
            { responsivePriority: 2, targets: 3 },
            { responsivePriority: 3, targets: 5 },
            { responsivePriority: 10, targets: [0, 2, 4, 6] },
        ],
        buttons: window.DT_EXPORT_BUTTONS,
    }",
    'extra' => "
        window.filterStatus = function(val) {
            dt.column(5).search(val, false, false).draw();
            document.getElementById('filter-status').value = val;
        };
    ",
])
<script>
function openRestock(id, nama) {
    document.getElementById('restock-id').value = id;
    document.getElementById('restock-nama').textContent = nama;
    document.getElementById('restock-modal').style.display = 'flex';
    setTimeout(() => document.getElementById('restock-jumlah').focus(), 0);
}
function closeRestock() { document.getElementById('restock-modal').style.display = 'none'; }
</script>
@endpush
