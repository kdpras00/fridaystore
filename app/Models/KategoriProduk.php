<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama'])]
class KategoriProduk extends Model
{
    protected $table = 'kategori_produk';

    public function produk()
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
}
