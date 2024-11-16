<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailKeranjang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;
use App\Models\Cart;

class DetailKeranjangController extends Controller
{
    // Mengambil semua detail keranjang
public function index()
{
    $detailKeranjangs = DetailKeranjang::all();
    return response()->json($detailKeranjangs);
}

// Mengambil detail keranjang berdasarkan ID
public function show($id)
{
    $detailKeranjang = DetailKeranjang::find($id);
    if (!$detailKeranjang) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }
    return response()->json($detailKeranjang);
}

    public function store(Request $request)
{
    Log::info('Memulai proses penyimpanan data ke Detail Keranjang');

    // Validasi input
    $validatedData = $request->validate([
        'quantity' => 'required|integer',
        'price_per_item' => 'required|numeric',
        'ID_keranjang' => 'required|exists:carts,ID_keranjang',
        'ID_produk' => 'required|exists:produk,ID_produk',
    ]);

    Log::info('Data berhasil divalidasi', ['data' => $validatedData]);

    // Simpan detail keranjang ke dalam database
    $detailKeranjang = new DetailKeranjang();
    $detailKeranjang->quantity = $request->quantity;
    $detailKeranjang->price_per_item = $request->price_per_item;
    $detailKeranjang->ID_keranjang = $request->ID_keranjang;
    $detailKeranjang->ID_produk = $request->ID_produk;
    $detailKeranjang->save();

    Log::info('Data berhasil disimpan ke database', ['detail_keranjang' => $detailKeranjang]);

    // Kembalikan response sukses
    return response()->json([
        'success' => true,
        'message' => 'Detail Keranjang berhasil ditambahkan',
        'data' => $detailKeranjang,
    ], 201);
}

    // Memperbarui data Detail Keranjang
public function update(Request $request, $id)
{
    $detailKeranjang = DetailKeranjang::find($id);

    if (!$detailKeranjang) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    // Validasi input
    $validatedData = $request->validate([
        'quantity' => 'required|integer',
        'price_per_item' => 'required|numeric',
    ]);

    // Update data
    $detailKeranjang->quantity = $request->quantity;
    $detailKeranjang->price_per_item = $request->price_per_item;
    $detailKeranjang->save();

    return response()->json([
        'success' => true,
        'message' => 'Data Detail Keranjang berhasil diperbarui',
        'data' => $detailKeranjang,
    ]);
}

// Menghapus Detail Keranjang
public function destroy($id)
{
    $detailKeranjang = DetailKeranjang::find($id);

    if (!$detailKeranjang) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    // Hapus data
    $detailKeranjang->delete();

    return response()->json([
        'success' => true,
        'message' => 'Data Detail Keranjang berhasil dihapus',
    ]);
}

    // Menampilkan semua produk dalam keranjang berdasarkan ID_user
    public function showDetailsByUser($ID_user)
    {
        $keranjang = Cart::where('ID_user', $ID_user)->get();
        $detailKeranjang = [];

        foreach ($keranjang as $item) {
            $produk = Produk::find($item->ID_produk);
            $totalPrice = $item->order_quantity * $produk->fish_price;

            $detailKeranjang[] = [
                'produk' => $produk,
                'quantity' => $item->order_quantity,
                'total_price' => $totalPrice
            ];
        }

        return response()->json($detailKeranjang);
    }

    public function getUserCartDetails($userId)
{
    // Mengambil detail keranjang pengguna berdasarkan ID pengguna
    $details = DetailKeranjang::whereHas('cart', function ($query) use ($userId) {
        $query->where('ID_user', $userId);
    })->with('produk')->get();

    // Menghitung total harga
    $total = $details->sum(fn($item) => $item->quantity * $item->price_per_item);

    return response()->json([
        'items' => $details,
        'total_price' => $total
    ]);
}

}

