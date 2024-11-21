<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Produk;
use App\Models\DetailKeranjang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    // Menambahkan produk ke keranjang
    public function addToCart(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'ID_produk' => 'required|exists:produk,ID_produk',
            'order_quantity' => 'required|integer|min:1'
        ]);

        // Mengecek apakah produk sudah ada di keranjang
        $existingCartItem = Cart::where('ID_user', $user->ID_user)
                                ->where('ID_produk', $request->ID_produk)
                                ->first();

        if ($existingCartItem) {
            // Jika produk sudah ada, tambahkan kuantitasnya
            $existingCartItem->order_quantity += $request->order_quantity;
            $existingCartItem->save();
            return response()->json(['message' => 'Produk ditambahkan ke keranjang. Kuantitas diperbarui.'], 200);
        } else {
            // Jika produk belum ada di keranjang, buat item baru
            $cartItem = Cart::create([
                'ID_user' => $user->ID_user,
                'ID_produk' => $request->ID_produk,
                'order_quantity' => $request->order_quantity
            ]);

            return response()->json(['message' => 'Produk berhasil ditambahkan ke keranjang.', 'cart_item' => $cartItem], 201);
        }
    }

    // Menampilkan isi keranjang
    public function showCart()
    {
        $user = Auth::user();

        $cartItems = Cart::with('produk')->where('ID_user', $user->ID_user)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang Anda kosong.'], 404);
        }

        return response()->json($cartItems);
    }

// Menghapus produk dari keranjang berdasarkan ID produk di tabel Cart
public function removeFromCart($ID_produk)
{
    // Mendapatkan user yang sedang login
    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Pengguna tidak terautentikasi.'], 401);
    }

    // Cari item keranjang berdasarkan ID produk dan ID user
    $cartItem = Cart::where('ID_user', $user->ID_user)
        ->where('ID_produk', $ID_produk)
        ->first();

    // Jika item tidak ditemukan, berikan pesan kesalahan
    if (!$cartItem) {
        return response()->json([
            'message' => 'Item keranjang tidak ditemukan.',
            'debug' => [
                'user_id' => $user->ID_user,
                'product_id' => $ID_produk,
                'cart_items' => Cart::where('ID_user', $user->ID_user)->get() // Debug untuk melihat semua item dalam keranjang user
            ]
        ], 404);
    }

    // Hapus item dari keranjang
    $cartItem->delete();

    // Berikan respons sukses
    return response()->json([
        'message' => 'Produk berhasil dihapus dari keranjang.',
        'deleted_item' => $cartItem
    ]);
}

    public function getCartDetails($cartId)
    {
        // Mengambil Cart beserta semua DetailKeranjang yang berhubungan
        $cart = Cart::with('details')->find($cartId);

        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        return response()->json($cart);
    }

    // Mengupdate kuantitas produk dalam keranjang
    public function updateQuantity(Request $request, $ID_keranjang)
    {
        $keranjang = Cart::findOrFail($ID_keranjang);
        $keranjang->order_quantity = $request->order_quantity;
        $keranjang->save();

        return response()->json(['message' => 'Kuantitas produk berhasil diupdate']);
    }

    // Mengurangi kuantitas produk dalam keranjang
    public function decreaseQuantity(Request $request)
    {
        $user = Auth::user();

         $request->validate([
            'ID_produk' => 'required|exists:produk,ID_produk',
            'quantity' => 'required|integer|min:1',
        ]);

         $cartItem = Cart::where('ID_user', $user->ID_user)
                        ->where('ID_produk', $request->ID_produk)
                        ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Produk tidak ditemukan di keranjang'], 404);
        }

        // Kurangi kuantitas
        if ($cartItem->order_quantity > $request->quantity) {
            $cartItem->order_quantity -= $request->quantity;
            $cartItem->save();

            return response()->json(['message' => 'Kuantitas berhasil dikurangi', 'cart_item' => $cartItem]);
        } else {
             $cartItem->delete();

            return response()->json(['message' => 'Produk dihapus dari keranjang karena kuantitas habis']);
        }
    }

    // public function addProductToCart(Request $request, $cartId)
    // {
    //     // Validasi data input
    //     $validatedData = $request->validate([
    //        'ID_produk' => 'required|exists:produk,ID_produk',
    //        'quantity' => 'required|integer|min:1',
    //     ]);

    //         // Mendapatkan produk berdasarkan ID_produk
    //     $product = Produk::find($validatedData['ID_produk']);
    //     if (!$product) {
    //         return response()->json(['message' => 'Produk tidak ditemukan'], 404);
    //     }

    //     // Debugging: Menampilkan ID keranjang dan memeriksa apakah keranjang ada
    //     Log::info('Mencoba menambah produk ke keranjang', ['cartId' => $cartId]);

    //     // Log produk untuk memastikan produk ditemukan
    //     Log::info('Produk ditemukan', ['ID_produk' => $product->ID_produk, 'price_per_item' => $product->fish_price]);

    //     // Menambahkan produk ke Cart (tabel Cart, bukan DetailKeranjang)
    //     $cart = Cart::findOrFail($cartId);
    //     $cart->ID_user = Auth::user()->ID_user;
    //     $cart->save(); // Simpan perubahan pada Car

    //     // Membuat atau memperbarui detail keranjang dengan produk dan quantity
    //     $detailKeranjang = DetailKeranjang::firstOrCreate(
    //         [
    //             'ID_keranjang' => $cartId,
    //             'ID_produk' => $validatedData['ID_produk']
    //         ],
    //         [
    //             'quantity' => $validatedData['quantity'],
    //             'price_per_item' => $product->fish_price,  // Menetapkan harga per item berdasarkan produk
    //         ]
    //     );

    //     // Jika detail keranjang sudah ada, update kuantitas dan harga total
    //     if (!$detailKeranjang->wasRecentlyCreated) {
    //         $detailKeranjang->quantity += $validatedData['quantity'];
    //     }

    //     // Update total harga setelah menambahkan kuantitas
    //     $detailKeranjang->total_price = $detailKeranjang->quantity * $detailKeranjang->price_per_item;
    //     $detailKeranjang->save();

    //     // Log untuk memastikan detail keranjang tersimpan dengan benar
    //     Log::info('Produk berhasil ditambahkan ke keranjang', ['detailKeranjang' => $detailKeranjang]);

    //     // Return response sukses dengan status 201
    //     return response()->json([
    //         'message' => 'Produk berhasil ditambahkan ke keranjang',
    //         'data' => $detailKeranjang
    //     ], 201);
    // }

    //    // Menambahkan produk ke dalam keranjang
    // public function MenambahkanKeKeranjang(Request $request)
    // {
    //     $produk = Produk::findOrFail($request->ID_produk);
    //     $keranjang = Cart::where('ID_user', $request->ID_user)
    //                      ->where('ID_produk', $request->ID_produk)
    //                      ->first();

    //     if ($keranjang) {
    //         // Jika produk sudah ada di keranjang, update kuantitasnya
    //         $keranjang->order_quantity += $request->order_quantity;
    //         $keranjang->save();
    //     } else {
    //         // Jika produk belum ada di keranjang, buat entri baru
    //         Cart::create([
    //             'ID_user' => $request->ID_user,
    //             'ID_produk' => $request->ID_produk,
    //             'order_quantity' => $request->order_quantity
    //         ]);
    //     }

    //     return response()->json(['message' => 'Produk berhasil ditambahkan ke keranjang']);
    // }



}

