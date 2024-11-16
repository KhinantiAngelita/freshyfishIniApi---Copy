<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use App\Models\Cart;
use App\Models\DetailKeranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    // Menampilkan pesanan untuk toko user yang login
    public function index()
    {
        $user = Auth::user();
        $pesanan = Pesanan::where('ID_toko', $user->ID_toko)->with('user', 'produk')->get();
        return response()->json($pesanan);
    }

    // Membuat pesanan baru
    public function store(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'ID_produk' => 'required|exists:produk,ID_produk',
            'order_quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        $produk = Produk::find($validatedData['ID_produk']);

        if ($produk->fish_weight < $validatedData['order_quantity']) {
            return response()->json(['message' => 'Jumlah produk tidak mencukupi'], 400);
        }

        $produk->fish_weight -= $validatedData['order_quantity'];
        $produk->save();

        $total_price = $produk->fish_price * $validatedData['order_quantity'];

        $pesanan = Pesanan::create([
            'ID_produk' => $produk->ID_produk,
            'order_quantity' => $validatedData['order_quantity'],
            'total_price' => $total_price,
            'order_date' => now(),
            'status' => 'pending',
            'ID_user' => $user->ID_user,
            'payment_method' => $validatedData['payment_method'],
        ]);

        return response()->json($pesanan, 201);
    }

    public function createOrder(Request $request)
    {
        $user = Auth::user();

        // Mengambil semua item di keranjang pengguna
        $cartItems = DetailKeranjang::whereHas('cart', function ($query) use ($user) {
            $query->where('ID_user', $user->ID_user);
        })->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong'], 400);
        }

        $total_price = $cartItems->sum(fn($item) => $item->quantity * $item->price_per_item);

        // Membuat nomor virtual account acak
        $virtualAccount = 'VA' . rand(10000000, 99999999);

        $pesanan = Pesanan::create([
            'ID_user' => $user->ID_user,
            'total_price' => $total_price,
            'order_date' => now(),
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'virtual_account' => $virtualAccount,
        ]);

        // Memindahkan data produk dari keranjang ke pesanan
        foreach ($cartItems as $item) {
            $pesanan->produk()->attach($item->ID_produk, [
                'quantity' => $item->quantity,
                'price_per_item' => $item->price_per_item
            ]);
        }

        // Menghapus isi keranjang setelah pesanan dibuat
        DetailKeranjang::whereHas('cart', function ($query) use ($user) {
            $query->where('ID_user', $user->ID_user);
        })->delete();

        return response()->json([
            'message' => 'Pesanan berhasil dibuat',
            'pesanan' => $pesanan,
            'virtual_account' => $virtualAccount
        ], 201);
    }

    // Membuat pesanan dan menghasilkan nomor virtual account
    public function membuatPesanan(Request $request)
    {
        // Mengambil data dari keranjang berdasarkan ID_user
       $detailKeranjang = DetailKeranjang::where('ID_detail_keranjang', $request->ID_detail_keranjang)->get();

        $totalPrice = 0;
        foreach ($detailKeranjang as $detail) {
            $totalPrice += $detail->quantity * $detail->produk->fish_price;
        }

        // Membuat pesanan baru
        $pesanan = Pesanan::create([
            'order_quantity' => count($detailKeranjang),
            'total_price' => $totalPrice,
            'order_date' => now(),
            'status' => 'pending', // Status bisa disesuaikan
            'ID_user' => $request->ID_user,
            'ID_keranjang' => $request->ID_keranjang,
            'payment_method' => $request->payment_method,
        ]);

        // Menambahkan nomor virtual account (random)
        $virtualAccountNumber = 'VA' . rand(1000000000000000, 9999999999999999);

        return response()->json([
            'pesanan' => $pesanan,
            'virtual_account' => $virtualAccountNumber
        ]);
    }

    // Fungsi untuk mengambil semua data pesanan yang ada di keranjang pengguna
public function getPesananFromCart()
{
    $user = Auth::user(); // Ambil data user yang sedang login

    // Mengambil semua item di keranjang untuk user yang sedang login
    $cartItems = DetailKeranjang::whereHas('cart', function ($query) use ($user) {
        $query->where('ID_user', $user->ID_user);
    })->get();

    // Cek apakah ada item di keranjang
    if ($cartItems->isEmpty()) {
        return response()->json(['message' => 'Keranjang kosong'], 400);
    }

    // Menghitung total harga seluruh produk yang dibeli
    $totalPrice = $cartItems->sum(function ($item) {
        return $item->quantity * $item->price_per_item;
    });

    // Menghasilkan nomor virtual account secara acak
    $virtualAccount = 'VA' . Str::random(10); // Nomor virtual account, dapat disesuaikan

    // Menyimpan pesanan baru
    $pesanan = Pesanan::create([
        'ID_user' => $user->ID_user,
        'total_price' => $totalPrice,
        'order_date' => now(),
        'status' => 'pending',  // Status bisa diubah jika perlu
        'payment_method' => 'transfer', // Metode pembayaran, bisa disesuaikan
        'virtual_account' => $virtualAccount, // Menyimpan nomor virtual account
    ]);

    // Mengaitkan produk dengan pesanan
    foreach ($cartItems as $item) {
        $pesanan->produk()->attach($item->ID_produk, [
            'quantity' => $item->quantity,
            'price_per_item' => $item->price_per_item
        ]);
    }

    // Menghapus item di keranjang setelah pesanan dibuat
    DetailKeranjang::whereHas('cart', function ($query) use ($user) {
        $query->where('ID_user', $user->ID_user);
    })->delete();

    // Kembalikan response sukses dengan data pesanan dan nomor virtual account
    return response()->json([
        'message' => 'Pesanan berhasil dibuat',
        'pesanan' => $pesanan,
        'total_price' => $totalPrice,
        'virtual_account' => $virtualAccount, // Menampilkan nomor virtual account
    ], 201);
}

}
