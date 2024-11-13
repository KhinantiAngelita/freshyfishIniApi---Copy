<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $primaryKey = 'ID_keranjang';

    protected $fillable = [
        'order_quantity',
        'ID_user',
        'ID_produk',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'ID_user');
    }

    // Relasi ke Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'ID_produk');
    }
}
