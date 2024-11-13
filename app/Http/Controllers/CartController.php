<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    // Menampilkan semua item keranjang
    public function index()
    {
        return Cart::with(['user', 'produk'])->get();
    }

    // Menyimpan item baru ke keranjang
    public function store(Request $request)
    {
        Log::info('Memulai proses penyimpanan data ke Cart');

        // Validasi input
        $validatedData = $request->validate([
            'order_quantity' => 'required|integer',
            'ID_user' => 'required|exists:users,ID_user',
            'ID_produk' => 'required|exists:produk,ID_produk',
        ]);

        Log::info('Data berhasil divalidasi', ['data' => $validatedData]);

        // Simpan data ke dalam database
        $cart = new Cart();
        $cart->order_quantity = $request->order_quantity;
        $cart->ID_user = $request->ID_user;
        $cart->ID_produk = $request->ID_produk;
        $cart->save();

        Log::info('Data berhasil disimpan ke database', ['cart' => $cart]);

        // Kembalikan response sukses
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan ke Cart',
            'data' => $cart,
        ], 201);
    }


    // Menampilkan item keranjang berdasarkan ID
    public function show($id)
    {
        $cart = Cart::with(['user', 'produk'])->findOrFail($id);
        return response()->json($cart);
    }

    // Mengupdate item keranjang
    public function update(Request $request, $id)
    {
        $cart = Cart::findOrFail($id);

        $request->validate([
            'order_quantity' => 'sometimes|required|integer|min:1',
            'ID_user' => 'sometimes|required|exists:users,ID_user',
            'ID_produk' => 'sometimes|required|exists:produk,ID_produk',
        ]);

        $cart->update($request->only(['order_quantity', 'ID_user', 'ID_produk']));

        return response()->json($cart);
    }

    // Menghapus item dari keranjang
    public function destroy($id)
    {
        $cart = Cart::findOrFail($id);
        $cart->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
