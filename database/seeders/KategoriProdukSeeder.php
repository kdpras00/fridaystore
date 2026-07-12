<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use Illuminate\Database\Seeder;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Jacket', 'Parka', 'Hoodie', 'Crewneck'] as $nama) {
            KategoriProduk::create(['nama' => $nama]);
        }
    }
}
