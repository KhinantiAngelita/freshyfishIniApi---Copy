<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesananProdukTable extends Migration
{
    public function up()
    {
        Schema::create('pesanan_produk', function (Blueprint $table) {
            $table->id('ID_pivot'); // Primary key
            $table->unsignedBigInteger('ID_pesanan'); // Foreign key ke tabel Pesanan
            $table->unsignedBigInteger('ID_produk'); // Foreign key ke tabel Produk
            $table->integer('quantity'); // Jumlah produk yang dipesan
            $table->decimal('price_per_item', 15, 2); // Harga satuan produk
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('ID_pesanan')->references('ID_pesanan')->on('pesanans')->onDelete('cascade');
            $table->foreign('ID_produk')->references('ID_produk')->on('produk')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pesanan_produk');
    }
}
