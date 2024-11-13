<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cart;

class CartSeeder extends Seeder
{
    public function run()
    {
        Cart::create([
            'order_quantity' => 2,
            'ID_user' => 1, // Sesuaikan dengan ID user yang valid
            'ID_produk' => 1 // Sesuaikan dengan ID produk yang valid
        ]);

        Cart::create([
            'order_quantity' => 5,
            'ID_user' => 2,
            'ID_produk' => 2
        ]);
    }
}
