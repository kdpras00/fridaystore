<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Seeder;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Jaket', 'Sepatu', 'Baju'] as $nama) {
            KategoriProduk::create(['nama' => $nama]);
        }
    }
}
