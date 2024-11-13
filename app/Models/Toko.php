<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Toko extends Model
{
    use HasFactory;

    // Menentukan kolom primary key
    protected $primaryKey = 'ID_toko'; // Ganti dengan nama kolom primary key yang benar

    // Tentukan nama tabel (opsional, jika Anda menggunakan nama tabel yang berbeda)
    protected $table = 'tokos';
    
    protected $fillable = ['store_name', 'store_address', 'product_category'];

}
