@extends('layouts.app')
@section('title', 'Edit Produk')
@section('content-class', 'form-page')
@section('hide-header', true)

@section('content')
<div class="page-intro" style="max-width:640px; width:100%;">
    <div class="page-intro-copy">
        <div class="page-intro-title">Edit Produk</div>
        <div class="page-intro-sub">Perubahan harga dan kategori akan langsung memengaruhi pencarian, laporan, dan proses transaksi kasir.</div>
    </div>
</div>

<div class="form-card">
    <div class="form-title">Edit Produk — {{ $produk->nama }}</div>
    <form method="POST" action="{{ route('produk.update', $produk) }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:16px;">
        @csrf @method('PUT')
        <div>
            <label class="form-label" for="produk-nama">Nama Produk</label>
            <input id="produk-nama" type="text" name="nama" value="{{ old('nama', $produk->nama) }}" class="form-input {{ $errors->has('nama') ? 'error' : '' }}" required>
            @error('nama') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label" for="produk-kategori">Kategori</label>
            <select id="produk-kategori" name="kategori_id" class="form-select" required>
                @foreach($kategori as $k)
                <option value="{{ $k->id }}" @selected(old('kategori_id', $produk->kategori_id) == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label class="form-label" for="harga-beli">Harga Beli (Rp)</label>
                <input id="harga-beli" type="number" name="harga_beli" value="{{ old('harga_beli', $produk->harga_beli) }}" class="form-input" min="0" required>
            </div>
            <div>
                <label class="form-label" for="harga-jual">Harga Jual (Rp)</label>
                <input id="harga-jual" type="number" name="harga_jual" value="{{ old('harga_jual', $produk->harga_jual) }}" class="form-input" min="0" required>
            </div>
        </div>
        <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label class="form-label" for="stok-minimum">Stok Minimum</label>
                <input id="stok-minimum" type="number" name="stok_minimum" value="{{ old('stok_minimum', $produk->stok_minimum) }}" class="form-input" min="0" required>
            </div>
            <div>
                <label class="form-label" for="stok-saat-ini">Stok Saat Ini</label>
                <input id="stok-saat-ini" type="text" value="{{ $produk->stok }} unit" class="form-input" readonly style="opacity:0.45;cursor:not-allowed;">
                <p style="font-size:10.5px;color:var(--color-ink-4);margin-top:3px;">Ubah via menu Stok</p>
            </div>
        </div>
        <div>
            <label class="form-label" for="produk-gambar">
                Ganti Gambar Utama
                <span style="font-size:11px;color:var(--color-ink-4);font-weight:400;">(opsional)</span>
            </label>
            @if($produk->gambar)
            <div style="margin-bottom:8px;">
                <img src="{{ asset('storage/'.$produk->gambar) }}" style="height:52px;border-radius:6px;object-fit:cover;" alt="">
            </div>
            @endif
            <input id="produk-gambar" type="file" name="gambar" accept="image/*" class="form-input">
        </div>
        <div>
            <label class="form-label" for="produk-galeri">
                Tambah Galeri (Multiple)
                <span style="font-size:11px;color:var(--color-ink-4);font-weight:400;">(opsional, file baru akan ditambahkan)</span>
            </label>
            @if($produk->galeri && $produk->galeri->count() > 0)
            <div style="margin-bottom:12px;display:flex;gap:12px;flex-wrap:wrap;">
                @foreach($produk->galeri as $gal)
                <div style="position:relative;">
                    <img src="{{ asset('storage/'.$gal->path) }}" style="height:64px;width:64px;border-radius:6px;object-fit:cover;border:1px solid var(--color-border);" alt="">
                    <label style="position:absolute;top:-6px;right:-6px;background:var(--color-danger);color:white;padding:2px 6px;border-radius:4px;font-size:10px;cursor:pointer;box-shadow:0 2px 4px rgba(0,0,0,0.2);">
                        <input type="checkbox" name="hapus_galeri[]" value="{{ $gal->id }}" style="display:none;" onchange="this.parentNode.previousElementSibling.style.opacity = this.checked ? '0.3' : '1'"> Hapus
                    </label>
                </div>
                @endforeach
            </div>
            @endif
            <input id="produk-galeri" type="file" name="galeri[]" accept="image/*" multiple class="form-input">
        </div>
        <div class="pos-scan-note" style="margin-top:0;">
            Simpan setelah cek ulang harga dan kategori. Kode produk ({{ $produk->kode_produk }}) tidak berubah.
        </div>
            <div style="display:flex;gap:12px;margin-top:24px;justify-content:flex-end;">
                <a href="{{ route('produk.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
    </form>
</div>
@endsection
