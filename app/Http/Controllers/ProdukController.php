<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProdukController extends Controller
{
    // Menampilkan daftar produk toko tertentu
    public function GetAllProduk()
    {
        return response()->json(Produk::all());
    }

    public function index()
    {
        $user = Auth::user();
        $produk = Produk::where('ID_toko', $user->ID_toko)->get();
        return response()->json($produk);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi data input
        $validatedData = $request->validate([
            'fish_type' => 'required|string',
            'fish_price' => 'required|numeric',
            'fish_weight' => 'required|numeric',
            'fish_photo' => 'required|image|mimes:jpg,png,jpeg', // Validasi file gambar
            'fish_description' => 'required|string',
            'habitat' => 'required|string',
        ]);

        // Menyimpan file gambar
        $fishPhoto = $request->file('fish_photo'); // Mengambil file gambar dari form
        $photoName = time() . '.' . $fishPhoto->getClientOriginalExtension(); // Membuat nama file unik
        $fishPhoto->storeAs('public/fish_photos', $photoName); // Menyimpan file gambar di folder 'public/fish_photos'

        // Membuat produk baru
        $produk = Produk::create([
            'fish_type' => $validatedData['fish_type'],
            'fish_price' => $validatedData['fish_price'],
            'fish_weight' => $validatedData['fish_weight'],
            'fish_photo' => $photoName, // Menyimpan nama file gambar
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

        // Validate the request data
        $validatedData = $request->validate([
            'fish_type' => 'sometimes|string',
            'fish_price' => 'sometimes|numeric',
            'fish_weight' => 'sometimes|numeric',
            'fish_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif', // Validate image file
            'fish_description' => 'sometimes|string',
            'habitat' => 'sometimes|string',
        ]);

        // Handle image upload if it exists
        if ($request->hasFile('fish_photo')) {
            // Check if there's an existing fish photo and delete it
            if (!empty($produk['fish_photo'])) {
                $existingFilePath = public_path('storage/fish_photos/' . $produk['fish_photo']);
                if (file_exists($existingFilePath)) {
                    unlink($existingFilePath); // Delete the existing file
                }
            }

            $fishPhoto = $request->file('fish_photo'); // Mengambil file gambar dari form
            $photoName = time() . '.' . $fishPhoto->getClientOriginalExtension(); // Membuat nama file unik
            $fishPhoto->storeAs('public/fish_photos', $photoName);
            $validatedData['fish_photo'] = $photoName;
        }

        // Update the product with validated data
        $produk->update([
            'fish_type' => $validatedData['fish_type'] ?? $produk->fish_type,
            'fish_price' => $validatedData['fish_price'] ?? $produk->fish_price,
            'fish_weight' => $validatedData['fish_weight'] ?? $produk->fish_weight,
            'fish_photo' => $validatedData['fish_photo'] ?? $produk->fish_photo,
            'fish_description' => $validatedData['fish_description'] ?? $produk->fish_description,
            'habitat' => $validatedData['habitat'] ?? $produk->habitat,
            'ID_toko' => $user->ID_toko,
        ]);

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
