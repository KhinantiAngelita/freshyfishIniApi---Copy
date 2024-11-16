<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    // Menampilkan daftar produk toko tertentu
    public function index()
    {
        $user = Auth::user();
        $produk = Produk::where('ID_toko', $user->ID_toko)->get();
        return response()->json($produk);
    }

    // Menambah produk baru
    public function store(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'fish_type' => 'required|string',
            'fish_price' => 'required|numeric',
            'fish_weight' => 'required|numeric',
            'fish_photo' => 'required|string',
            'fish_description' => 'required|string',
            'habitat' => 'required|string',
        ]);

        $produk = Produk::create([
            'fish_type' => $validatedData['fish_type'],
            'fish_price' => $validatedData['fish_price'],
            'fish_weight' => $validatedData['fish_weight'],
            'fish_photo' => $validatedData['fish_photo'],
            'fish_description' => $validatedData['fish_description'],
            'habitat' => $validatedData['habitat'],
            'ID_toko' => $user->ID_toko,
        ]);

        return response()->json($produk, 201);
    }

    // Mengedit produk
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $user = Auth::user();
        if ($produk->ID_toko != $user->ID_toko) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk mengedit produk ini'], 403);
        }

        $validatedData = $request->validate([
            'fish_type' => 'string',
            'fish_price' => 'numeric',
            'fish_weight' => 'numeric',
            'fish_photo' => 'string',
            'fish_description' => 'string',
            'habitat' => 'string',
        ]);

        $produk->update($validatedData);

        return response()->json($produk);
    }

    // Menghapus produk
    public function delete($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $user = Auth::user();
        if ($produk->ID_toko != $user->ID_toko) {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk mengedit produk ini'], 403);
        }

        $produk->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
