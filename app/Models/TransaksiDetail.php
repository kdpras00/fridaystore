<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['transaksi_id', 'produk_id', 'nama_produk', 'harga_jual', 'qty', 'subtotal'])]
class TransaksiDetail extends Model
{
    protected $table = 'transaksi_detail';

    protected function casts(): array
    {
        return [
            'harga_jual' => 'decimal:2',
            'subtotal'   => 'decimal:2',
        ];
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
