<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\User;
use App\Models\Cart;
use App\Models\DetailKeranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{

    public function checkout(Request $request)
    {
        $user = Auth::user();

        // Ambil semua keranjang milik user yang sedang login
        $cartItems = Cart::where('ID_user', $user->ID_user)->get();

        // Pastikan ada barang di keranjang
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong'], 400);
        }

        // Hitung total harga
        $totalPrice = 0;
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->produk; // Ambil produk dari relasi keranjang
            $totalPrice += $cartItem->order_quantity * $product->fish_price; // Hitung harga total

            // Cek apakah stok ikan cukup
            if ($product->fish_weight < $cartItem->order_quantity) {
                return response()->json([
                    'message' => 'Stok ikan tidak cukup untuk produk ' . $product->fish_type
                ], 400);
            }
        }

        // Generate nomor virtual account secara acak
        $virtualAccount = 'VA' . rand(1000000000000000, 9999999999999999);

        // Buat pesanan baru
        $pesanan = Pesanan::create([
            'ID_user' => $user->ID_user,
            'total_price' => $totalPrice,
            'order_date' => now(),
            'status' => 'pending',  // Status pesanan bisa disesuaikan
            'payment_method' => $request->payment_method, // Misal: transfer, e-wallet, dll
            'virtual_account' => $virtualAccount, // Nomor virtual account
        ]);

        // Kaitkan produk yang dibeli dengan pesanan dan kurangi stok ikan
        foreach ($cartItems as $cartItem) {
            // Ambil produk dari keranjang
            $product = $cartItem->produk;

            // Kurangi stok ikan berdasarkan kuantitas yang dipesan
            $product->fish_weight -= $cartItem->order_quantity;
            $product->save();

            // Kaitkan produk dengan pesanan
            $pesanan->produk()->attach($cartItem->ID_produk, [
                'quantity' => $cartItem->order_quantity,
                'price_per_item' => $product->fish_price
            ]);
        }

        // Hapus barang di keranjang setelah checkout
        Cart::where('ID_user', $user->ID_user)->delete();

        // Kembalikan response sukses dengan data pesanan dan nomor virtual account
        return response()->json([
            'message' => 'Pesanan berhasil dibuat',
            'pesanan' => $pesanan,
            'virtual_account' => $virtualAccount, // Nomor virtual account
        ], 201);
    }

    public function markAndShowOrderHistory($ID_user)
    {
        $orders = Pesanan::where('ID_user', $ID_user)->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Tidak ada pesanan yang berstatus pending'], 404);
        }

        foreach ($orders as $order) {
            $order->status = 'complete';
            $order->save();
        }

        $completedOrders = Pesanan::where('ID_user', $ID_user)->get();

        if ($completedOrders->isEmpty()) {
            return response()->json(['message' => 'Tidak ada histori pesanan yang ditemukan'], 404);
        }

        // Menambahkan detail produk untuk setiap pesanan
        foreach ($completedOrders as $order) {
            // Ambil produk yang terkait dengan pesanan melalui relasi many-to-many
            $order->produk = $order->produk()->get();

            // Format detail produk untuk tiap pesanan
            $order->produk_details = $order->produk->map(function ($product) {
                return [
                    'fish_type' => $product->fish_type,
                    'quantity' => $product->pivot->quantity, // Ambil kuantitas dari pivot table
                    'price_per_item' => $product->pivot->price_per_item, // Ambil harga per item dari pivot table
                    'total_price' => $product->pivot->quantity * $product->pivot->price_per_item // Hitung total harga per produk
                ];
            });
        }

        // Kembalikan response dengan data pesanan dan detail produk
        return response()->json([
            'ID_user' => $ID_user,
            'order_history' => $completedOrders
        ]);
    }


public function showAllOrdersByStore()
{
    // Ambil data user yang sedang login
    $user = Auth::user();

    // Pastikan user memiliki toko
    if (!$user->ID_toko) {
        return response()->json(['message' => 'User tidak memiliki toko.'], 400);
    }

    // Ambil semua pesanan yang terkait dengan toko pengguna yang sedang login
    $orders = Pesanan::whereHas('produk', function ($query) use ($user) {
        $query->where('ID_toko', $user->ID_toko);
    })->get();

    // Cek jika tidak ada pesanan untuk toko tersebut
    if ($orders->isEmpty()) {
        return response()->json(['message' => 'Tidak ada pesanan untuk toko ini.'], 404);
    }

    // Mengembalikan data pesanan
    return response()->json([
        'ID_toko' => $user->ID_toko,
        'orders' => $orders
    ]);
}



//     //belum dipakai
//     // Menampilkan pesanan untuk toko user yang login
//     public function index()
//     {
//         $user = Auth::user();
//         $pesanan = Pesanan::where('ID_toko', $user->ID_toko)->with('user', 'produk')->get();
//         return response()->json($pesanan);
//     }

//     // ga jadi dipake,
//     // Membuat pesanan baru
//     public function store(Request $request)
//     {
//         $user = Auth::user();
//         $validatedData = $request->validate([
//             'ID_produk' => 'required|exists:produk,ID_produk',
//             'order_quantity' => 'required|integer|min:1',
//             'payment_method' => 'required|string',
//         ]);

//         $produk = Produk::find($validatedData['ID_produk']);

//         if ($produk->fish_weight < $validatedData['order_quantity']) {
//             return response()->json(['message' => 'Jumlah produk tidak mencukupi'], 400);
//         }

//         $produk->fish_weight -= $validatedData['order_quantity'];
//         $produk->save();

//         $total_price = $produk->fish_price * $validatedData['order_quantity'];

//         $pesanan = Pesanan::create([
//             'ID_produk' => $produk->ID_produk,
//             'order_quantity' => $validatedData['order_quantity'],
//             'total_price' => $total_price,
//             'order_date' => now(),
//             'status' => 'pending',
//             'ID_user' => $user->ID_user,
//             'payment_method' => $validatedData['payment_method'],
//         ]);

//         return response()->json($pesanan, 201);
//     }

//     //ga jadi dipake
//     public function createOrder(Request $request)
//     {
//         $user = Auth::user();

//         $cartItems = DetailKeranjang::whereHas('cart', function ($query) use ($user) {
//             $query->where('ID_user', $user->ID_user);
//         })->get();

//         if ($cartItems->isEmpty()) {
//             return response()->json(['message' => 'Keranjang kosong'], 400);
//         }

//         $total_price = $cartItems->sum(fn($item) => $item->quantity * $item->price_per_item);

//         $virtualAccount = 'VA' . rand(10000000, 99999999);

//         $pesanan = Pesanan::create([
//             'ID_user' => $user->ID_user,
//             'total_price' => $total_price,
//             'order_date' => now(),
//             'status' => 'pending',
//             'payment_method' => $request->payment_method,
//             'virtual_account' => $virtualAccount,
//         ]);

//         foreach ($cartItems as $item) {
//             $pesanan->produk()->attach($item->ID_produk, [
//                 'quantity' => $item->quantity,
//                 'price_per_item' => $item->price_per_item
//             ]);
//         }

//         DetailKeranjang::whereHas('cart', function ($query) use ($user) {
//             $query->where('ID_user', $user->ID_user);
//         })->delete();

//         return response()->json([
//             'message' => 'Pesanan berhasil dibuat',
//             'pesanan' => $pesanan,
//             'virtual_account' => $virtualAccount
//         ], 201);
//     }

//     public function membuatPesanan(Request $request)
//     {
//        $detailKeranjang = DetailKeranjang::where('ID_detail_keranjang', $request->ID_detail_keranjang)->get();

//         $totalQuantity = $detailKeranjang->sum('quantity');
//         $totalPrice = 0;
//         foreach ($detailKeranjang as $detail) {
//             $totalPrice += $detail->quantity * $detail->produk->fish_price;
//         }

//         $pesanan = Pesanan::create([
//             'order_quantity' => $totalQuantity,
//             'total_price' => $totalPrice,
//             'order_date' => now(),
//             'status' => 'pending', // Status bisa disesuaikan
//             'ID_user' => $request->ID_user,
//             // 'ID_keranjang' => $cartItems->first()->ID_keranjang,     // 'ID_keranjang' => $request->ID_keranjang,
//             'payment_method' => $request->payment_method,
//         ]);

//         $virtualAccountNumber = 'VA' . rand(1000000000000000, 9999999999999999);

//         return response()->json([
//             'pesanan' => $pesanan,
//             'virtual_account' => $virtualAccountNumber
//         ]);
//     }

// public function getPesananFromCart()
// {
//     $user = Auth::user(); // Ambil data user yang sedang login

//     // Mengambil semua item di keranjang untuk user yang sedang login
//     $cartItems = DetailKeranjang::whereHas('cart', function ($query) use ($user) {
//         $query->where('ID_user', $user->ID_user);
//     })->get();

//     // Cek apakah ada item di keranjang
//     if ($cartItems->isEmpty()) {
//         return response()->json(['message' => 'Keranjang kosong'], 400);
//     }

//     // Menghitung total harga seluruh produk yang dibeli
//     $totalPrice = $cartItems->sum(function ($item) {
//         return $item->quantity * $item->price_per_item;
//     });

//     // Menghasilkan nomor virtual account secara acak
//     $virtualAccount = 'VA' . rand(1000000000000000, 9999999999999999); // Nomor virtual account, dapat disesuaikan

//     // Menyimpan pesanan baru
//     $pesanan = Pesanan::create([
//         'ID_user' => $user->ID_user,
//         'total_price' => $totalPrice,
//         'order_date' => now(),
//         'status' => 'pending',  // Status bisa diubah jika perlu
//         'payment_method' => 'transfer', // Metode pembayaran, bisa disesuaikan
//         'virtual_account' => $virtualAccount, // Menyimpan nomor virtual account
//     ]);

//     // Mengaitkan produk dengan pesanan
//     foreach ($cartItems as $item) {
//         $pesanan->produk()->attach($item->ID_produk, [
//             'quantity' => $item->quantity,
//             'price_per_item' => $item->price_per_item
//         ]);
//     }

//     // Menghapus item di keranjang setelah pesanan dibuat
//     DetailKeranjang::whereHas('cart', function ($query) use ($user) {
//         $query->where('ID_user', $user->ID_user);
//     })->delete();

//     // Kembalikan response sukses dengan data pesanan dan nomor virtual account
//     return response()->json([
//         'message' => 'Pesanan berhasil dibuat',
//         'pesanan' => $pesanan,
//         'total_price' => $totalPrice,
//         'virtual_account' => $virtualAccount, // Menampilkan nomor virtual account
//     ], 201);
// }




}
