<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('detail_keranjangs', function (Blueprint $table) {
        $table->id('ID_detail_keranjang');
        $table->integer('quantity');
        $table->decimal('price_per_item', 8, 2);  // Menyimpan harga per item
        $table->unsignedBigInteger('ID_keranjang');
        $table->unsignedBigInteger('ID_produk');
        $table->foreign('ID_keranjang')->references('ID_keranjang')->on('carts')->onDelete('cascade');
        $table->foreign('ID_produk')->references('ID_produk')->on('produk')->onDelete('cascade');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_keranjangs');
    }
};
