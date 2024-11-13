<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailKeranjang extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'price_per_item',
        'ID_keranjang',
        'ID_produk',
    ];
}
