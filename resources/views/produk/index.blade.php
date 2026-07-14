@extends('layouts.app')
@section('title', 'Produk')
@section('page-title', 'Kelola Produk')
@section('page-sub', 'Daftar produk, harga jual, dan stok barang toko.')
@section('page-actions')
<a href="{{ route('produk.create') }}" class="btn btn-primary">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
    </svg>
    Tambah Produk
</a>
@endsection

@section('content')

{{-- Category quick-filter --}}
<div class="card filter-card" style="margin-bottom:16px;">
    <div class="filter-bar" style="align-items:flex-end;">
        <div>
            <label class="form-label" for="filter-kategori">Kategori</label>
            <select id="filter-kategori" class="form-select" style="width:auto;" onchange="filterKategori(this.value)">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $k)
                <option value="{{ $k->nama }}">{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" onclick="filterKategori('')" class="btn btn-ghost">Reset</button>
    </div>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table id="tbl-produk" class="data-table" style="width:100%">
            <thead><tr>
                <th class="dt-no-export" style="width:56px;">Foto</th>
                <th>Kode</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th class="dt-no-export" style="text-align:right;">Aksi</th>
                <th></th>
            </tr></thead>
            <tbody>
            @foreach($produk as $p)
            <tr>
                <td class="dt-no-export">
                    <div style="width:44px;height:44px;border-radius:6px;background:var(--color-surface-3);
                                border:1px solid var(--color-border-subtle);display:flex;align-items:center;
                                justify-content:center;overflow:hidden;flex-shrink:0;">
                        @if($p->gambar)
                        <img src="{{ asset('storage/'.$p->gambar) }}" style="width:100%;height:100%;object-fit:cover;" alt="{{ $p->nama }}">
                        @else
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="var(--color-ink-4)" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        @endif
                    </div>
                </td>
                <td style="font-size:12.5px;color:var(--color-ink-3);font-family:var(--font-mono);">{{ $p->kode_produk }}</td>
                <td style="font-weight:500;color:var(--color-ink);">{{ $p->nama }}</td>
                <td style="font-size:13.5px;color:var(--color-ink-2);">{{ $p->kategori->nama }}</td>
                <td style="font-family:var(--font-mono);font-size:13px;font-weight:500;color:var(--color-ink-2);"
                    data-order="{{ $p->harga_beli }}">
                    Rp {{ number_format($p->harga_beli, 0, ',', '.') }}
                </td>
                <td style="font-family:var(--font-mono);font-size:13px;font-weight:500;color:var(--color-ink);"
                    data-order="{{ $p->harga_jual }}">
                    Rp {{ number_format($p->harga_jual, 0, ',', '.') }}
                </td>
                <td data-order="{{ $p->stok }}">
                    <span style="font-size:13px;color:var(--color-ink-2);display:inline-flex;align-items:center;gap:6px;">
                        @if($p->isStokRendah())
                        <span style="width:6px;height:6px;border-radius:50%;background:var(--color-danger);flex-shrink:0;" title="Stok Rendah"></span>
                        @endif
                        {{ $p->stok }}
                    </span>
                </td>
                <td class="dt-no-export" style="text-align:right;white-space:nowrap;">
                    <a href="{{ route('produk.edit', $p) }}" class="btn btn-ghost btn-sm">Edit</a>
                    <form id="del-prd-{{ $p->id }}" method="POST" action="{{ route('produk.destroy', $p) }}" style="display:none;">@csrf @method('DELETE')</form>
                    <button onclick="confirmDelete('del-prd-{{ $p->id }}','{{ addslashes($p->nama) }}')" class="btn btn-danger btn-sm" style="cursor:pointer;">Hapus</button>
                </td>
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
    'tableId' => 'tbl-produk',
    'config'  => "{
        order: [[2, 'asc']],
        columnDefs: [
            { className: 'dtr-control', orderable: false, targets: -1 },
            { orderable: false, targets: [0, 7] },
            { responsivePriority: 1, targets: 2 },
            { responsivePriority: 2, targets: 5 },
            { responsivePriority: 3, targets: 6 },
            { responsivePriority: 10, targets: [0, 1, 3, 4, 7] },
        ],
        buttons: window.DT_EXPORT_BUTTONS,
    }",
    'extra' => "
        window.filterKategori = function(val) {
            dt.column(3).search(val, false, false).draw();
            document.getElementById('filter-kategori').value = val;
        };
    ",
])
@endpush
