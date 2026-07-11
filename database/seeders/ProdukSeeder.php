<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = KategoriProduk::pluck('id', 'nama');

        $produk = [
            ['kategori' => 'Jaket',  'nama' => 'Jaket Hoodie Hitam',  'beli' => 120000, 'jual' => 175000, 'stok' => 18],
            ['kategori' => 'Jaket',  'nama' => 'Jaket Bomber Olive',   'beli' => 150000, 'jual' => 220000, 'stok' => 12],
            ['kategori' => 'Jaket',  'nama' => 'Jaket Denim Biru',     'beli' => 135000, 'jual' => 195000, 'stok' => 10],
            ['kategori' => 'Sepatu', 'nama' => 'Sneakers Putih',       'beli' => 180000, 'jual' => 275000, 'stok' => 16],
            ['kategori' => 'Sepatu', 'nama' => 'Sepatu Lari Abu',      'beli' => 210000, 'jual' => 325000, 'stok' =>  9],
            ['kategori' => 'Sepatu', 'nama' => 'Sepatu Kasual Hitam',  'beli' => 160000, 'jual' => 245000, 'stok' => 14],
            ['kategori' => 'Baju',   'nama' => 'Kaos Polos Putih',     'beli' =>  45000, 'jual' =>  85000, 'stok' => 30],
            ['kategori' => 'Baju',   'nama' => 'Kemeja Flanel Merah',  'beli' =>  90000, 'jual' => 145000, 'stok' => 15],
            ['kategori' => 'Baju',   'nama' => 'Polo Shirt Navy',      'beli' =>  75000, 'jual' => 120000, 'stok' => 20],
        ];

        foreach ($produk as $p) {
            $kategoriId = $kategori[$p['kategori']];
            Produk::create([
                'kategori_id'  => $kategoriId,
                'kode_produk'  => Produk::generateKode($kategoriId),
                'nama'         => $p['nama'],
                'harga_beli'   => $p['beli'],
                'harga_jual'   => $p['jual'],
                'stok'         => $p['stok'],
                'stok_minimum' => 5,
            ]);
        }
    }
}
