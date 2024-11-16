<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\Cart;
use Carbon\Carbon;

class PesananSeeder extends Seeder
{
    public function run()
    {
        // Ambil data user pertama (bisa disesuaikan dengan data yang ada di database)
        $user = User::first();

        // Ambil data produk pertama
        $produk = Produk::first();

        // Ambil data toko pertama
        $toko = Toko::first();

        // Ambil data keranjang pertama
        $keranjang = Cart::first();

        // Buat data pesanan dengan relasi yang sudah ada
        Pesanan::create([
            'ID_user' => $user->ID_user,  // Ambil ID_user dari user yang pertama
            //'ID_produk' => $produk->ID_produk,  // Ambil ID_produk dari produk yang pertama
            'order_quantity' => 2,  // Jumlah produk yang dipesan
            'total_price' => $produk->fish_price * 2,  // Total harga (harga per item dikali jumlah)
            'order_date' => Carbon::now(),  // Waktu saat pesanan dibuat
            'status' => 'pending',  // Status pesanan
            //'ID_toko' => $toko->ID_toko,  // Ambil ID_toko dari toko yang pertama
            'ID_keranjang' => $keranjang->ID_keranjang,  // Ambil ID_keranjang dari keranjang yang pertama
            'payment_method' => 'credit_card',  // Metode pembayaran (bisa disesuaikan)
        ]);

        // Anda bisa membuat lebih banyak data pesanan jika diperlukan
        // Pesanan::create([...]);
    }
}
