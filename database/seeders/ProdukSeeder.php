<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        Produk::create([
            'fish_type' => 'Ikan Tuna',
            'fish_price' => 100000,
            'fish_weight' => 10,
            'fish_photo' => 'path/to/tuna.jpg',
            'fish_description' => 'Ikan laut segar, kualitas premium',
            'habitat' => 'Laut',
            'ID_toko' => 1 // Sesuaikan dengan ID toko yang valid
        ]);

        Produk::create([
            'fish_type' => 'Ikan Lele',
            'fish_price' => 50000,
            'fish_weight' => 20,
            'fish_photo' => 'path/to/lele.jpg',
            'fish_description' => 'Ikan air tawar segar',
            'habitat' => 'Air Tawar',
            'ID_toko' => 2
        ]);

        Produk::create([
            'fish_type' => 'Ikan Bandeng',
            'fish_price' => 75000,
            'fish_weight' => 15,
            'fish_photo' => 'path/to/bandeng.jpg',
            'fish_description' => 'Ikan payau berkualitas',
            'habitat' => 'Air Payau',
            'ID_toko' => 6
        ]);
    }
}
