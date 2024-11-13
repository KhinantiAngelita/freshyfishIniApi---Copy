<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';
    protected $primaryKey = 'ID_pesanan';

    protected $fillable = ['ID_produk', 'order_quantity', 'total_price', 'order_date', 'status', 'ID_user', 'payment_method'];

    public function user()
    {
        return $this->belongsTo(User::class, 'ID_user');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'ID_produk');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'ID_keranjang');
    }
}
