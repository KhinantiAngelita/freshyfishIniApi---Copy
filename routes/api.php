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
use App\Http\Controllers\ArticleController;


//AUTH
Route::prefix('auth')->group(function() {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::get('/me', [UserController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user/{id}', [UserController::class, 'showProfile'])->middleware('auth:sanctum');
    Route::put('/user/{id}', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');
    Route::delete('/user/{id}', [UserController::class, 'deleteProfile'])->middleware('auth:sanctum');
    Route::post('/user/upload-image', [UserController::class, 'uploadImage'])->middleware('auth:sanctum');
});

//TOKO
Route::post('/toko', [TokoController::class, 'openStore'])->middleware('auth:sanctum');
Route::get('/toko', [TokoController::class, 'show'])->middleware('auth:sanctum');
Route::put('/toko/{id}', [TokoController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/toko/delete/{id}', [TokoController::class, 'closeStore'])->middleware('auth:sanctum');
// Route::delete('/toko/{id}', [TokoController::class, 'delete'])->middleware('auth:sanctum');
// Route::post('/nambahtoko', [TokoController::class, 'store'])->middleware('auth:sanctum');


//PRODUK
Route::get('/produk', [ProdukController::class, 'GetAllProduk'])->middleware('auth:sanctum');
Route::get('/produksaya', [ProdukController::class, 'index'])->middleware('auth:sanctum');
Route::get('/produk/{id}', [ProdukController::class, 'getProdukById'])->middleware('auth:sanctum');
Route::post('/produk', [ProdukController::class, 'store'])->middleware('auth:sanctum');
Route::post('/produk/{id}', [ProdukController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/produk/{id}', [ProdukController::class, 'delete'])->middleware('auth:sanctum');
Route::get('/produk/habitat/{habitat}', [ProdukController::class, 'getProdukByHabitat'])->middleware('auth:sanctum');



//KERANJANG
Route::post('/keranjang', [CartController::class, 'addToCart'])->middleware('auth:sanctum');
Route::get('/keranjang', [CartController::class, 'showCart'])->middleware('auth:sanctum');
// Route::get('/keranjang/{id}/details', [CartController::class, 'getCartDetails'])->middleware('auth:sanctum');
// Route::post('/keranjang/{id}/add-product', [CartController::class, 'addProductToCart'])->middleware('auth:sanctum');
Route::delete('/keranjang/{id}', [CartController::class, 'removeFromCart'])->middleware('auth:sanctum');
// Route::post('/keranjang/Nambah-produk', [CartController::class, 'MenambahkanKeKeranjang'])->middleware('auth:sanctum');
Route::put('/updatekuantitas/{id}', [CartController::class, 'updateQuantity'])->middleware('auth:sanctum');
Route::post('/keranjang/kurangin', [CartController::class, 'decreaseQuantity'])->middleware('auth:sanctum');

//DETAIL KERANJANG
Route::get('/detail-keranjang/{ID_user}', [DetailKeranjangController::class, 'showDetailsByUser'])->middleware('auth:sanctum');


//PESANAN
Route::post('/pesanan/buatpesanan', [PesananController::class, 'checkout'])->middleware('auth:sanctum');
Route::get('/pesanan/histori/{id}', [PesananController::class, 'markAndShowOrderHistory'])->middleware('auth:sanctum');
Route::get('/pesanan/histori', [PesananController::class, 'showAllOrdersByStore'])->middleware('auth:sanctum');
Route::get('/pesanan', [PesananController::class, 'index'])->middleware('auth:sanctum');
Route::post('/pesanan', [PesananController::class, 'store'])->middleware('auth:sanctum');
Route::post('/membuatpesanan', [PesananController::class, 'membuatPesanan'])->middleware('auth:sanctum');
Route::post('/pesanan/create', [PesananController::class, 'createOrder'])->middleware('auth:sanctum');
Route::post('/pesanan/makeOrder', [PesananController::class, 'getPesananFromCart'])->middleware('auth:sanctum');

//ARTIKEL
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::put('/articles/{id}', [ArticleController::class, 'update']);
    Route::delete('/articles/{id}', [ArticleController::class, 'destroy']);
});

//Roles
Route::post('/user/upgrade-to-seller', [RoleController::class, 'upgradeToSeller'])->middleware('auth:sanctum');
Route::get('roles', [RoleController::class, 'index'])->middleware('auth:sanctum');
Route::post('roles', [RoleController::class, 'store'])->middleware('auth:sanctum');
Route::get('roles/{id}', [RoleController::class, 'show'])->middleware('auth:sanctum');
Route::put('roles/{id}', [RoleController::class, 'update'])->middleware('auth:sanctum');
Route::delete('roles/{id}', [RoleController::class, 'destroy'])->middleware('auth:sanctum');


//Detail keranjang sub
Route::get('detail-keranjang', [DetailKeranjangController::class, 'index'])->middleware('auth:sanctum');
// Route::get('detail-keranjang/{id}', [DetailKeranjangController::class, 'show'])->middleware('auth:sanctum');
Route::post('detail-keranjang', [DetailKeranjangController::class, 'store'])->middleware('auth:sanctum');
Route::put('detail-keranjang/{id}', [DetailKeranjangController::class, 'update'])->middleware('auth:sanctum');
Route::delete('detail-keranjang/{id}', [DetailKeranjangController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/users/{userId}/cart-details', [DetailKeranjangController::class, 'getUserCartDetails'])->middleware('auth:sanctum');
