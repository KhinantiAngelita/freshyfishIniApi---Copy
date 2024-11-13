<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    // Menampilkan semua role
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    // Menambahkan role baru
    public function store(Request $request)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        $role = Role::create([
            'role_name' => $request->role_name,
        ]);

        return response()->json($role, 201);
    }

    // Menampilkan role berdasarkan ID
    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    // Mengupdate role
    public function update(Request $request, $id)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
        ]);

        $role = Role::findOrFail($id);
        $role->role_name = $request->role_name;
        $role->save();

        return response()->json($role);
    }

    // Menghapus role
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }

    // public function upgradeToSeller(Request $request)
    // {
    //     $user = Auth::user();

    //     if ($user->ID_role == Role::where('role_name', 'penjual')->first()->ID_role) {
    //         return response()->json(['message' => 'User sudah menjadi penjual']);
    //     }

    //     $role = Role::where('role_name', 'penjual')->first();
    //     $user->ID_role = $role->ID_role;
    //     $user->save();

    //     return response()->json(['message' => 'Role berhasil diubah menjadi penjual']);
    // }

    
    // Mengubah role user menjadi penjual ketika membuka toko
    public function upgradeToSeller(Request $request)
    {
        $user = Auth::user();

        if ($user->ID_role == Role::where('role_name', 'penjual')->first()->ID_role) {
            return response()->json(['message' => 'User sudah menjadi penjual']);
        }

        $role = Role::where('role_name', 'penjual')->first();
        $user->ID_role = $role->ID_role;
        $user->save();

        return response()->json(['message' => 'Role berhasil diubah menjadi penjual']);
    }

}
