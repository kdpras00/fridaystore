<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['kategori_id', 'kode_produk', 'nama', 'harga_beli', 'harga_jual', 'stok', 'stok_minimum', 'gambar'])]
class Produk extends Model
{
    protected $table = 'produk';

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'harga_jual' => 'decimal:2',
        ];
    }

    /**
     * Generate kode produk otomatis: PREFIX + urutan per kategori.
     * Contoh: JKT001, SPT002, BJU001
     */
    public static function generateKode(int $kategoriId): string
    {
        $namaKategori = \App\Models\KategoriProduk::find($kategoriId)?->nama ?? 'PRD';
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $namaKategori), 0, 3));
        $prefix = str_pad($prefix, 3, 'X');

        $last = static::where('kategori_id', $kategoriId)
            ->where('kode_produk', 'like', $prefix . '%')
            ->max('kode_produk');

        $next = $last ? ((int) substr($last, 3)) + 1 : 1;

        return $prefix . str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_id');
    }

    public function stokMutasi()
    {
        return $this->hasMany(StokMutasi::class);
    }

    public function transaksiDetail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    public function galeri()
    {
        return $this->hasMany(ProdukGambar::class);
    }

    public function isStokRendah(): bool
    {
        return $this->stok <= $this->stok_minimum;
    }
}
