<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    // Fungsi registrasi user
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $role = Role::where('role_name', 'pembeli')->first();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone_number' => $validatedData['phone_number'],
            'password' => Hash::make($validatedData['password']),
            'ID_role' => $role->ID_role,
        ]);

        return response()->json($user, 201);
    }

    // Fungsi login user
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            return response()->json(['user' => $user, 'token' => $user->createToken('API Token')->plainTextToken]);
        } else {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    // Fungsi logout user
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    // Menampilkan profil user
    public function showProfile($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    //Mengambil Profile User
    public function me(Request $request) {
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $request->user()
        ], Response::HTTP_OK);
    }

    // Mengupdate profil user
    public function updateProfile(Request $request, $id)
    {
        $user = User::find($id);
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'phone_number' => 'string',
            'address' => 'string',
        ]);

        $user->update($validatedData);
        return response()->json($user);
    }

    // Menghapus user
    public function deleteProfile($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
