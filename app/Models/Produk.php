<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;

    // Nama tabel jika tidak sesuai dengan konvensi
    protected $table = 'produk';
    protected $primaryKey = 'ID_produk';

    // Kolom yang dapat diisi secara mass-assignment
    protected $fillable = [
        'fish_type',
        'fish_price',
        'fish_weight',
        'fish_photo',
        'fish_description',
        'habitat',
        'ID_toko'
    ];

    // Relasi ke Toko
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'ID_toko', 'ID_toko');
    }

    public function pesanans()
{
    return $this->belongsToMany(Pesanan::class, 'pesanan_produk', 'ID_produk', 'ID_pesanan')
                ->withPivot('quantity', 'price_per_item');
}

}
