<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';
    protected $primaryKey = 'ID_pesanan';

    protected $fillable = ['total_price', 'order_date', 'status', 'ID_user', 'payment_method', 'virtual_account'];

    public function user()
    {
        return $this->belongsTo(User::class, 'ID_user');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'ID_keranjang');
    }

    public function produk()
{
    return $this->belongsToMany(Produk::class, 'pesanan_produk', 'ID_pesanan', 'ID_produk')
                ->withPivot('quantity', 'price_per_item');
}

}
