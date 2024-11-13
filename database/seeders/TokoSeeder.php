<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Toko;

class TokoSeeder extends Seeder
{
    public function run()
    {
        Toko::create([
            'store_name' => 'Toko Ikan Segar',
            'store_address' => 'Jl. Laut No.1',
            'product_category' => 'Ikan Laut'
        ]);

        Toko::create([
            'store_name' => 'Toko Ikan Tawar',
            'store_address' => 'Jl. Danau Indah No.3',
            'product_category' => 'Ikan Tawar'
        ]);

        Toko::create([
            'store_name' => 'Toko Ikan Payau',
            'store_address' => 'Jl. Payau Sejahtera No.7',
            'product_category' => 'Ikan Payau'
        ]);
    }
}
