@extends('layouts.app')
@section('title', 'Stok')
@section('page-title', 'Stok Barang')
@section('page-sub', 'Kelola stok barang dan riwayat mutasi masuk/keluar.')
@section('page-actions')
<a href="{{ route('stok.riwayat') }}" class="btn btn-ghost">
    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
    Riwayat
</a>
@endsection

@section('content')

@php($lowStockCount = $produk->getCollection()->filter(fn ($item) => $item->isStokRendah())->count())

<div class="page-intro">
    <div class="page-intro-copy">
        <div class="page-intro-title">Stok Barang</div>
        <div class="page-intro-sub">Pantau persediaan, restock yang mendekati batas minimum, dan lihat status barang secara cepat tanpa berpindah menu.</div>
    </div>
    <div class="page-intro-meta">
        <span class="info-chip">Total <strong>{{ number_format($produk->total()) }}</strong></span>
        <span class="info-chip">Stok rendah di halaman ini <strong>{{ number_format($lowStockCount) }}</strong></span>
    </div>
</div>

<div class="card filter-card">
    <form method="GET" class="filter-bar">
        <div>
            <label class="form-label" for="stok-search">Cari</label>
            <input id="stok-search" type="text" name="search" value="{{ request('search') }}" placeholder="Nama produk…" class="form-input" style="width:220px;">
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-ghost">Cari</button>
            @if(request('search')) <a href="{{ route('stok.index') }}" class="btn btn-ghost">Reset</a> @endif
        </div>
    </form>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr>
                <th>Kode</th>
                <th>Nama Produk</th>
                {{-- ponytail: plain text category instead of badge --}}
                <th>Kategori</th>
                <th style="text-align:right;">Stok</th>
                <th class="th-gap" style="text-align:right;">Min.</th>
                <th class="th-gap">Status</th>
                <th style="text-align:right;">Aksi</th>
            </tr></thead>
            <tbody>
            @forelse($produk as $p)
            <tr>
                <td style="font-size:12.5px;color:var(--color-ink-3);font-family:var(--font-mono);">{{ $p->kode_produk }}</td>
                <td style="font-weight:500;color:var(--color-ink);">{{ $p->nama }}</td>
                {{-- ponytail: plain text category instead of badge --}}
                <td style="font-size:13.5px;color:var(--color-ink-2);">{{ $p->kategori->nama }}</td>
                <td style="text-align:right;font-family:var(--font-mono);font-size:13px;font-weight:400;color:var(--color-ink-2);">
                    {{ $p->stok }}
                </td>
                <td class="td-gap" style="text-align:right;font-size:12px;color:var(--color-ink-4);">{{ $p->stok_minimum }}</td>
                <td class="td-gap">
                    {{-- ponytail: plain text/colored warning instead of badge --}}
                    <span style="font-size:13px;font-weight:400;color:var(--color-ink-2);display:inline-flex;align-items:center;gap:6px;">
                        <span style="width:6px;height:6px;border-radius:50%;background:{{ $p->isStokRendah() ? 'var(--color-danger)' : 'var(--color-success)' }};"></span>
                        {{ $p->isStokRendah() ? 'Rendah' : 'Aman' }}
                    </span>
                </td>
                <td style="text-align:right;">
                    <button onclick="openRestock({{ $p->id }},'{{ addslashes($p->nama) }}')" class="btn btn-success btn-sm" style="cursor:pointer;">+ Restock</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="padding:0;">
                    <div class="empty-state">
                        <div class="empty-state-title">Tidak ada data stok</div>
                        <div class="empty-state-copy">Coba ubah kata kunci pencarian atau kembali ke daftar stok penuh.</div>
                        <div class="empty-state-actions">
                            <a href="{{ route('stok.index') }}" class="btn btn-ghost">Reset Filter</a>
                            <a href="{{ route('stok.riwayat') }}" class="btn btn-primary">Lihat Riwayat</a>
                        </div>
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
                <label class="form-label" for="restock-keterangan">Keterangan <span style="color:var(--color-ink-4);">(opsional)</span></label>
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
