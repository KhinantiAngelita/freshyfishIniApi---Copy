<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailKeranjang extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_detail_keranjang';

    protected $fillable = [
        'quantity',
        //'price_per_item',
        'ID_keranjang',
        'ID_produk',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'ID_keranjang', 'ID_keranjang');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'ID_produk', 'ID_produk');
    }

}
