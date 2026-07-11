@extends('layouts.app')
@section('title', 'Riwayat Mutasi Stok')
@section('page-title', 'Riwayat Mutasi Stok')
@section('page-actions')
<a href="{{ route('stok.index') }}" class="btn btn-ghost">← Kembali</a>
@endsection

@section('content')
<div class="page-intro">
    <div class="page-intro-copy">
        <div class="page-intro-title">Riwayat Mutasi Stok</div>
        <div class="page-intro-sub">Pantau pergerakan stok masuk dan keluar untuk audit operasional, pembelian, dan koreksi barang.</div>
    </div>
    <div class="page-intro-meta">
        <span class="info-chip">Filter aktif <strong>{{ request('produk_id') || request('tipe') ? 'Ya' : 'Tidak' }}</strong></span>
        <span class="info-chip">Tipe <strong>Masuk / Keluar</strong></span>
    </div>
</div>

<div class="card filter-card">
    <form method="GET" class="filter-bar">
        <div>
            <label class="form-label">Produk</label>
            <select name="produk_id" class="form-select" style="width:auto;">
                <option value="">Semua Produk</option>
                @foreach($produkList as $p)
                <option value="{{ $p->id }}" @selected(request('produk_id') == $p->id)>{{ $p->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Tipe</label>
            <select name="tipe" class="form-select" style="width:auto;">
                <option value="">Semua Tipe</option>
                <option value="masuk" @selected(request('tipe')=='masuk')>Masuk</option>
                <option value="keluar" @selected(request('tipe')=='keluar')>Keluar</option>
            </select>
        </div>
        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn btn-ghost">Filter</button>
            <a href="{{ route('stok.riwayat') }}" class="btn btn-ghost">Reset</a>
        </div>
    </form>
</div>

<div class="card table-card">
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Produk</th>
                    <th>Tipe</th>
                    <th style="text-align:right;">Jumlah</th>
                    <th>Keterangan</th>
                    <th>Oleh</th>
                </tr>
            </thead>
            <tbody>
            @forelse($mutasi as $m)
            <tr>
                <td style="font-family:var(--font-mono);font-size:12px;color:var(--color-ink-3);white-space:nowrap;">
                    {{ $m->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="td-primary">{{ $m->produk->nama }}</td>
                <td>
                    <span style="font-size:13.5px;font-weight:600;color:{{ $m->tipe === 'masuk' ? 'var(--color-success)' : 'var(--color-danger)' }};">
                        {{ $m->tipe === 'masuk' ? '↑ Masuk' : '↓ Keluar' }}
                    </span>
                </td>
                <td style="text-align:right;font-family:var(--font-mono);font-weight:600;color:var(--color-ink);">
                    {{ $m->jumlah }}
                </td>
                <td style="color:var(--color-ink-3);">{{ $m->keterangan ?? '—' }}</td>
                <td style="font-size:12px;color:var(--color-ink-3);">{{ $m->user->name }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:0;">
                    <div class="empty-state">
                        <div class="empty-state-title">Tidak ada riwayat</div>
                        <div class="empty-state-copy">Coba ubah filter produk atau tipe mutasi untuk menemukan data yang dibutuhkan.</div>
                    </div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($mutasi->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--color-border-subtle);">{{ $mutasi->links() }}</div>
    @endif
</div>
@endsection
