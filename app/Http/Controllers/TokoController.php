<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokoController extends Controller
{
    // Membuka toko untuk user
    public function openStore(Request $request)
    {
        $user = Auth::user();

        if ($user->ID_role == 'penjual') {
            return response()->json(['message' => 'Anda sudah memiliki toko']);
        }

        $validatedData = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
            'product_category' => 'required|string',
        ]);

        $toko = Toko::create([
            'store_name' => $validatedData['store_name'],
            'store_address' => $validatedData['store_address'],
            'product_category' => $validatedData['product_category'],
        ]);

        $user->ID_toko = $toko->ID_toko;
        $user->ID_role = 2;
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
        $toko = Toko::find($id);
        $validatedData = $request->validate([
            'store_name' => 'string|max:255',
            'store_address' => 'string',
            'product_category' => 'string',
        ]);

        $toko->update($validatedData);
        return response()->json($toko);
    }

    // Hapus toko
    public function delete($id)
    {
        $toko = Toko::find($id);
        $toko->delete();
        return response()->json(['message' => 'Toko deleted successfully']);
    }
}
