<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Memanggil seeder untuk tabel roles, users, dan toko
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TokoSeeder::class,
            ProdukSeeder::class,
            CartSeeder::class,
            PesananSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
