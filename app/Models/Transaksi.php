<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['no_invoice', 'kasir_id', 'total_harga', 'diskon', 'total_bayar', 'uang_bayar', 'kembalian'])]
class Transaksi extends Model
{
    protected $table = 'transaksi';

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'diskon'      => 'decimal:2',
            'total_bayar' => 'decimal:2',
            'uang_bayar'  => 'decimal:2',
            'kembalian'   => 'decimal:2',
        ];
    }

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function detail()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
