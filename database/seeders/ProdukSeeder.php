<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $k = KategoriProduk::pluck('id', 'nama');

        $produk = [
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island 118WN T.Co+ Old Dyed Jacket',
                'beli'     => 2800000,
                'jual'     => 3500000,
                'stok'     => 1,
                'gambar'   => 'Stone island 118WN T.co + old Used Size MEDIUM.jpg',
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'ALBAM Smock Jacket Cream',
                'beli'     => 1200000,
                'jual'     => 1750000,
                'stok'     => 3,
                'gambar'   => 'ALBAM Smock jacket New with tag Size SMALL up LARGE.jpg',
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Reflective Liquid 30th Anniversary',
                'beli'     => 4500000,
                'jual'     => 5800000,
                'stok'     => 1,
                'gambar'   => 'Stone island Reflective Liquid 30th anniversery Used Size SMALL.jpg',
                'galeri'   => ['Stone island Reflective Liquid 30th anniversery Used Size SMALL(2).jpg']
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Naslan Light Watro Navy',
                'beli'     => 3200000,
                'jual'     => 4200000,
                'stok'     => 1,
                'gambar'   => 'NASLAN LIGHT WATRO (NAVY) Size M.jpg',
                'galeri'   => [
                    'NASLAN LIGHT WATRO (NAVY) Size M(2).jpg',
                    'NASLAN LIGHT WATRO (NAVY) Size M(3).jpg'
                ]
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Reflective Camouflage 30th Anniversary Blue',
                'beli'     => 5000000,
                'jual'     => 6500000,
                'stok'     => 1,
                'gambar'   => 'Stone island Reflective camouflage 30th anniversery Used 80pct Size MEDIUM.jpg',
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Reflective Camouflage 30th Anniversary',
                'beli'     => 5200000,
                'jual'     => 6800000,
                'stok'     => 1,
                'gambar'   => 'REFLECTIVE CAMOUFLAGE.jpg',
                'galeri'   => ['REFLECTIVE CAMOUFLAGE2.jpg.jpg']
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Naslan Light Warltro Olive',
                'beli'     => 3000000,
                'jual'     => 3900000,
                'stok'     => 1,
                'gambar'   => 'Stone island Naslan Light Warltro NEW Size MEDIUM.jpg',
                'galeri'   => ['Stone island Naslan Light Warltro NEW Size MEDIUM(2).jpg']
            ],
            [
                'kategori' => 'Jacket',
                'nama'     => 'Stone Island Shadow Project Hollow Core Jacket Navy',
                'beli'     => 3500000,
                'jual'     => 4500000,
                'stok'     => 1,
                'gambar'   => 'Stone island shadow project hollow core Used Size MEDIUM.jpg',
                'galeri'   => ['Stone island shadow project hollow core Used Size MEDIUM(2).jpg']
            ],
            [
                'kategori' => 'Parka',
                'nama'     => 'Stone Island Membrana TC Parka Jacket Navy',
                'beli'     => 3800000,
                'jual'     => 5000000,
                'stok'     => 1,
                'gambar'   => 'Stone island membrana tc parka jacket Used Size SMALL.jpg',
            ],
            [
                'kategori' => 'Hoodie',
                'nama'     => 'Stone Island Camouflage Hoodie Grey',
                'beli'     => 2200000,
                'jual'     => 2900000,
                'stok'     => 1,
                'gambar'   => 'Stone island camouflage hoodie Used good conditionn Size LARGE.jpg',
            ],
            [
                'kategori' => 'Hoodie',
                'nama'     => 'Stone Island Striped Hooded Sweater Blue',
                'beli'     => 1800000,
                'jual'     => 2400000,
                'stok'     => 1,
                'gambar'   => 'Stone island striped hooded Used well Size MEDIUM.jpg',
            ],
            [
                'kategori' => 'Hoodie',
                'nama'     => 'Stone Island Shadow Project Zip Hoodie Olive',
                'beli'     => 3200000,
                'jual'     => 4100000,
                'stok'     => 1,
                'gambar'   => 'Stone island Shadaw project New Size SMALL.jpg',
            ],
            [
                'kategori' => 'Crewneck',
                'nama'     => 'Stone Island Shadow Project Crewneck Black',
                'beli'     => 2500000,
                'jual'     => 3200000,
                'stok'     => 1,
                'gambar'   => 'Stone island shadow project crewneck Used with minus Size SMALL.jpg',
            ],
            [
                'kategori' => 'Crewneck',
                'nama'     => 'Stone Island Tye Dye Crewneck',
                'beli'     => 2800000,
                'jual'     => 3600000,
                'stok'     => 1,
                'gambar'   => 'Stone island tye dye crewneck New Size LARGE.jpg',
            ]
        ];

        if (!File::exists(storage_path('app/public/produk/galeri'))) {
            File::makeDirectory(storage_path('app/public/produk/galeri'), 0755, true);
        }

        foreach ($produk as $p) {
            $kategoriId = $k[$p['kategori']];
            
            $gambarPath = null;
            if (isset($p['gambar'])) {
                $src = public_path('images/' . $p['gambar']);
                if (File::exists($src)) {
                    $gambarPath = 'produk/' . $p['gambar'];
                    File::copy($src, storage_path('app/public/' . $gambarPath));
                }
            }

            $prod = Produk::create([
                'kategori_id'  => $kategoriId,
                'kode_produk'  => Produk::generateKode($kategoriId),
                'nama'         => $p['nama'],
                'harga_beli'   => $p['beli'],
                'harga_jual'   => $p['jual'],
                'stok'         => $p['stok'],
                'stok_minimum' => 1,
                'gambar'       => $gambarPath,
            ]);

            if (isset($p['galeri'])) {
                foreach ($p['galeri'] as $gal) {
                    $src = public_path('images/' . $gal);
                    if (File::exists($src)) {
                        $galPath = 'produk/galeri/' . $gal;
                        File::copy($src, storage_path('app/public/' . $galPath));
                        $prod->galeri()->create(['path' => $galPath]);
                    }
                }
            }
        }
    }
}
