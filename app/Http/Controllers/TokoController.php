<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TokoController extends Controller
{
    // Membuka toko untuk user
    public function openStore(Request $request)
    {
        $user = Auth::user();

        if ($user->ID_role == 2 ) {
            return response()->json(['message' => 'Anda sudah memiliki toko']);
        }

        $validatedData = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'description_store' => 'required|string',
        ]);

        $toko = Toko::create([
            'store_name' => $validatedData['store_name'],
            'store_address' => $validatedData['store_address'],
            'description_store' => $validatedData['description_store'],
        ]);

        $user->ID_toko = $toko->ID_toko;
        $user->ID_role = '2';
        $user->save();

        return response()->json($toko, 201);
    }

    // Menampilkan detail toko
    public function show($id)
    {
        $toko = Toko::find($id);
        return response()->json($toko);
    }

    // Update detail toko
    public function update(Request $request, $id)
    {
        // Validasi data yang diterima
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string|max:255',
            'description_store' => 'required|string|max:255',
        ]);

         // Menambahkan log untuk melihat data yang diterima
        Log::info('Request data:', $request->all()); // Log request data yang diterima dari client

        // Cari toko berdasarkan ID
        $toko = Toko::find($id);

        // Menambahkan log untuk melihat data toko yang ditemukan
        Log::info('Toko data before update:', $toko->toArray()); // Log data toko sebelum update

        // Periksa jika toko tidak ditemukan
        if (!$toko) {
            return response()->json(['message' => 'Toko tidak ditemukan'], 404);
        }

        // Update data toko
        $toko->store_name = $request->input('store_name');
        $toko->store_address = $request->input('store_address');
        $toko->description_store = $request->input('description_store');

        // Menambahkan log untuk melihat data yang akan disimpan
        Log::info('Toko data after update:', $toko->toArray()); // Log data toko setelah update

        // Simpan perubahan ke database
        $toko->save();

        return response()->json([
            'message' => 'Toko berhasil diperbarui',
            'toko' => $toko
        ]);
    }


    // Hapus toko
    public function delete($id)
    {
        $toko = Toko::find($id);
        $toko->delete();
        return response()->json(['message' => 'Toko deleted successfully']);
    }

    public function store(Request $request)
    {
        // Validasi input toko
        $validatedData = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'description_store' => 'required|string',
    ]);

        // Membuat toko baru
        $toko = Toko::create([
            'store_name' => $validatedData['store_name'],
            'store_address' => $validatedData['store_address'],
            'description_store' => $validatedData['description_store'],
        ]);

        // Menghubungkan toko dengan user yang sedang login
        $user = auth()->user(); // Mendapatkan user yang sedang login
        $user->ID_toko = $toko->ID_toko; // Menambahkan ID_toko ke user
        $user->save(); // Menyimpan perubahan

        // Mengembalikan response
        return response()->json([
            'message' => 'Toko berhasil dibuat dan dihubungkan dengan user',
            'toko' => $toko
        ], 201);
    }

}
