<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
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
}
