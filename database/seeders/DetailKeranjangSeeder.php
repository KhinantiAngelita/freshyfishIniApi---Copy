<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cart;
use App\Models\Produk;
use App\Models\DetailKeranjang;

class DetailKeranjangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ID_keranjang dan ID_produk yang diisi ada di database untuk menghindari error foreign key

        // Ambil contoh keranjang dan produk untuk disambungkan
        $cart = Cart::first();
        $produk = Produk::first();

        // Buat data detail keranjang
        DetailKeranjang::create([
            'quantity' => 2,
            'ID_keranjang' => $cart->ID_keranjang,
            'ID_produk' => $produk->ID_produk,
        ]);

        // Contoh lainnya
        DetailKeranjang::create([
            'quantity' => 3,
            'ID_keranjang' => $cart->ID_keranjang,
            'ID_produk' => $produk->ID_produk,
        ]);

        // Anda bisa menambahkan lebih banyak data seeder sesuai kebutuhan
    }
    }
