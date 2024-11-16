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
            'description_store' => 'Ikan Laut'
        ]);

        Toko::create([
            'store_name' => 'Toko Ikan Tawar',
            'store_address' => 'Jl. Danau Indah No.3',
            'description_store' => 'Ikan Tawar'
        ]);

        Toko::create([
            'store_name' => 'Toko Ikan Payau',
            'store_address' => 'Jl. Payau Sejahtera No.7',
            'description_store' => 'Ikan Payau'
        ]);
    }
}
