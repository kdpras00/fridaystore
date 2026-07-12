<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $k = KategoriProduk::pluck('id', 'nama');

        // 15 produk @plusdfd — Stone Island secondhand Indonesia
        // Harga estimasi pasar SI secondhand. Sesuaikan via halaman Edit Produk.
        $produk = [
            // ── JACKET (8 item) ──────────────────────────────────────────────
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island 118WN T.Co+ Old Dyed Jacket',
                'kondisi'  => 'Used',
                'size'     => 'M',
                'beli'     => 2800000,
                'jual'     => 3500000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'ALBAM Smock Jacket Cream',
                'kondisi'  => 'New with tag',
                'size'     => 'S – L',
                'beli'     => 1200000,
                'jual'     => 1750000,
                'stok'     => 3,
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Reflective Liquid 30th Anniversary',
                'kondisi'  => 'Used',
                'size'     => 'S',
                'beli'     => 4500000,
                'jual'     => 5800000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Naslan Light Warltro Navy',
                'kondisi'  => 'New',
                'size'     => 'M',
                'beli'     => 3200000,
                'jual'     => 4200000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Reflective Camouflage 30th Anniversary Blue',
                'kondisi'  => 'Used 80%',
                'size'     => 'M',
                'beli'     => 5000000,
                'jual'     => 6500000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Reflective Camouflage 30th Anniversary',
                'kondisi'  => 'Used 80%',
                'size'     => 'M',
                'beli'     => 5200000,
                'jual'     => 6800000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Naslan Light Watro Olive',
                'kondisi'  => 'New',
                'size'     => 'M',
                'beli'     => 3000000,
                'jual'     => 3900000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Shadow Project Hollow Core Jacket Navy',
                'kondisi'  => 'Used',
                'size'     => 'M',
                'beli'     => 3500000,
                'jual'     => 4500000,
                'stok'     => 1,
            ],
            // ── PARKA (1 item) ───────────────────────────────────────────────
            [
                'kategori' => 'Parka',
                'nama'     => 'Stone Island Membrana TC Parka Jacket Navy',
                'kondisi'  => 'Used',
                'size'     => 'S',
                'beli'     => 3800000,
                'jual'     => 5000000,
                'stok'     => 1,
            ],
            // ── HOODIE (3 item) ──────────────────────────────────────────────
            [
                'kategori' => 'Hoodie',
                'nama'     => 'Stone Island Camouflage Hoodie Grey',
                'kondisi'  => 'Used good condition',
                'size'     => 'L',
                'beli'     => 2200000,
                'jual'     => 2900000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Hoodie',
                'nama'     => 'Stone Island Striped Hooded Sweater Blue',
                'kondisi'  => 'Used well',
                'size'     => 'M',
                'beli'     => 1800000,
                'jual'     => 2400000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Hoodie',
                'nama'     => 'Stone Island Shadow Project Zip Hoodie Olive',
                'kondisi'  => 'New',
                'size'     => 'S',
                'beli'     => 3200000,
                'jual'     => 4100000,
                'stok'     => 1,
            ],
            // ── CREWNECK (3 item) ────────────────────────────────────────────
            [
                'kategori' => 'Crewneck',
                'nama'     => 'Stone Island Shadow Project Crewneck Black',
                'kondisi'  => 'Used with minus',
                'size'     => 'S',
                'beli'     => 2500000,
                'jual'     => 3200000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Crewneck',
                'nama'     => 'Stone Island Tye Dye Crewneck',
                'kondisi'  => 'New',
                'size'     => 'L',
                'beli'     => 2800000,
                'jual'     => 3600000,
                'stok'     => 1,
            ],
            [
                'kategori' => 'Crewneck',
                'nama'     => 'Stone Island Shadow Project Crewneck Olive',
                'kondisi'  => 'New',
                'size'     => 'S',
                'beli'     => 3000000,
                'jual'     => 3900000,
                'stok'     => 1,
            ],
        ];

        foreach ($produk as $p) {
            $kategoriId = $k[$p['kategori']];
            Produk::create([
                'kategori_id'  => $kategoriId,
                'kode_produk'  => Produk::generateKode($kategoriId),
                'nama'         => $p['nama'],
                'harga_beli'   => $p['beli'],
                'harga_jual'   => $p['jual'],
                'stok'         => $p['stok'],
                'stok_minimum' => 1,
            ]);
        }
    }
}
