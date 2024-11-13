<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DetailKeranjangController;
use App\Http\Controllers\PesananController;


Route::prefix('auth')->group(function() {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/me', [UserController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user/{id}', [UserController::class, 'showProfile'])->middleware('auth:sanctum');
    Route::put('/user/{id}', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');
    Route::delete('/user/{id}', [UserController::class, 'deleteProfile'])->middleware('auth:sanctum');

    ## ini dipake untuk route yang lama yang belum logic nya diubah GPT
    // Route::post('/login', [UserController::class, 'login'])->name('login');
    // Route::post('/register', [UserController::class, 'register'])->name('register');
    // Route::middleware('auth:sanctum')->group(function() {
    //     Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    //     Route::get('/me', [UserController::class, 'me'])->name('me');
    //     Route::delete('/delete', [UserController::class, 'delete'])->name('delete');
    //     Route::put('/update', [UserController::class, 'update'])->name('update');
    // });
});

Route::post('/toko', [TokoController::class, 'openStore'])->middleware('auth:sanctum');
Route::get('/toko/{id}', [TokoController::class, 'show'])->middleware('auth:sanctum');
Route::put('/toko/{id}', [TokoController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/toko/{id}', [TokoController::class, 'delete'])->middleware('auth:sanctum');

Route::post('/user/upgrade-to-seller', [RoleController::class, 'upgradeToSeller'])->middleware('auth:sanctum');
Route::get('roles', [RoleController::class, 'index'])->middleware('auth:sanctum');
Route::post('roles', [RoleController::class, 'store'])->middleware('auth:sanctum');
Route::get('roles/{id}', [RoleController::class, 'show'])->middleware('auth:sanctum');
Route::put('roles/{id}', [RoleController::class, 'update'])->middleware('auth:sanctum');
Route::delete('roles/{id}', [RoleController::class, 'destroy'])->middleware('auth:sanctum');


Route::get('/produk', [ProdukController::class, 'index'])->middleware('auth:sanctum');
Route::post('/produk', [ProdukController::class, 'store'])->middleware('auth:sanctum');
Route::put('/produk/{id}', [ProdukController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/produk/{id}', [ProdukController::class, 'delete'])->middleware('auth:sanctum');

Route::get('/pesanan', [PesananController::class, 'index'])->middleware('auth:sanctum');
Route::post('/pesanan', [PesananController::class, 'store'])->middleware('auth:sanctum');

Route::get('/keranjangs', [CartController::class, 'index'])->middleware('auth:sanctum');
Route::post('/keranjangs', [CartController::class, 'store'])->middleware('auth:sanctum');
Route::get('/keranjangs/{id}', [CartController::class, 'show'])->middleware('auth:sanctum');
Route::put('/keranjangs/{id}', [CartController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/keranjangs/{id}', [CartController::class, 'destroy'])->middleware('auth:sanctum');





// Proteksi rute Role, Toko, dan Produk dengan middleware auth:sanctum
// Route::middleware('auth:sanctum')->group(function () {

    Route::get('tokos', [TokoController::class, 'index']);
    Route::post('tokos', [TokoController::class, 'store']);
    Route::get('tokos/{id}', [TokoController::class, 'show']);
    Route::put('tokos/{id}', [TokoController::class, 'update']);
    Route::delete('tokos/{id}', [TokoController::class, 'destroy']);

    // Route::get('produk', [ProdukController::class, 'index']);
    // Route::get('produk/{id}', [ProdukController::class, 'show']);
    // Route::post('produk', [ProdukController::class, 'store']);
    // Route::put('produk/{id}', [ProdukController::class, 'update']);
    // Route::delete('produk/{id}', [ProdukController::class, 'destroy']);
// });


Route::get('detail-keranjangs', [DetailKeranjangController::class, 'index']);
Route::get('detail-keranjangs/{id}', [DetailKeranjangController::class, 'show']);
Route::post('detail-keranjangs', [DetailKeranjangController::class, 'store']);
Route::put('detail-keranjangs/{id}', [DetailKeranjangController::class, 'update']);
Route::delete('detail-keranjangs/{id}', [DetailKeranjangController::class, 'destroy']);

// Route::get('pesanans', [PesananController::class, 'index']);
// Route::get('pesanans/{id}', [PesananController::class, 'show']);
// Route::post('pesanans', [PesananController::class, 'store']);
// Route::put('pesanans/{id}', [PesananController::class, 'update']);
// Route::delete('pesanans/{id}', [PesananController::class, 'destroy']);


