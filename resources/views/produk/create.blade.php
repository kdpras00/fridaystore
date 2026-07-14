@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('content-class', 'form-page')
@section('hide-header', true)

@section('content')
<div class="page-intro" style="max-width:640px; width:100%;">
    <div class="page-intro-copy">
        <div class="page-intro-title">Tambah Produk</div>
        <div class="page-intro-sub">Isi nama, kategori, harga, dan stok awal. Kode produk di-generate otomatis.</div>
    </div>
</div>

<div class="form-card">
    <div class="form-title">Tambah Produk Baru</div>
    <form method="POST" action="{{ route('produk.store') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:16px;">
        @csrf
        <div>
            <label class="form-label" for="produk-nama">Nama Produk</label>
            <input id="produk-nama" type="text" name="nama" value="{{ old('nama') }}" class="form-input {{ $errors->has('nama') ? 'error' : '' }}" placeholder="cth: Jaket Hoodie Hitam" required>
            @error('nama') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label" for="produk-kategori">Kategori</label>
            <select id="produk-kategori" name="kategori_id" class="form-select {{ $errors->has('kategori_id') ? 'error' : '' }}" required>
                <option value="">-- Pilih --</option>
                @foreach($kategori as $k)
                <option value="{{ $k->id }}" @selected(old('kategori_id') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
            @error('kategori_id') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label class="form-label" for="harga-beli">Harga Beli (Rp)</label>
                <input id="harga-beli" type="number" name="harga_beli" value="{{ old('harga_beli', 0) }}" class="form-input" min="0" required>
            </div>
            <div>
                <label class="form-label" for="harga-jual">Harga Jual (Rp)</label>
                <input id="harga-jual" type="number" name="harga_jual" value="{{ old('harga_jual', 0) }}" class="form-input" min="0" required>
            </div>
        </div>
        <div class="form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label class="form-label" for="stok-awal">Stok Awal</label>
                <input id="stok-awal" type="number" name="stok" value="{{ old('stok', 0) }}" class="form-input" min="0" required>
            </div>
            <div>
                <label class="form-label" for="stok-minimum">Stok Minimum</label>
                <input id="stok-minimum" type="number" name="stok_minimum" value="{{ old('stok_minimum', 5) }}" class="form-input" min="0" required>
            </div>
        </div>
        <div>
            <label class="form-label" for="produk-gambar">
                Gambar Utama
                <span style="font-size:11px;color:var(--color-ink-4);font-weight:400;">(opsional, maks 2MB)</span>
            </label>
            <input id="produk-gambar" type="file" name="gambar" accept="image/*" class="form-input">
            @error('gambar') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="form-label" for="produk-galeri">
                Galeri Tambahan (Multiple)
                <span style="font-size:11px;color:var(--color-ink-4);font-weight:400;">(opsional, bisa pilih banyak file)</span>
            </label>
            <input id="produk-galeri" type="file" name="galeri[]" accept="image/*" multiple class="form-input">
            @error('galeri.*') <p class="form-error">{{ $message }}</p> @enderror
        </div>
        <div class="pos-scan-note" style="margin-top:0;">
            Kode produk di-generate otomatis dari nama kategori (contoh: Jaket → JAK001, Sepatu → SEP001).
        </div>
            <div style="display:flex;gap:12px;margin-top:24px;justify-content:flex-end;">
                <a href="{{ route('produk.index') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>
    </form>
</div>
@endsection
