<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['produk_id', 'user_id', 'tipe', 'jumlah', 'keterangan'])]
class StokMutasi extends Model
{
    protected $table = 'stok_mutasi';

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
