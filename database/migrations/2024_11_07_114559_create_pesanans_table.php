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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id('ID_pesanan');
            $table->unsignedBigInteger('ID_produk');
            $table->foreign('ID_produk')->references('ID_produk')->on('produk')->onDelete('cascade');
            $table->integer('order_quantity');
            $table->decimal('total_price', 10, 2);
            $table->dateTime('order_date');
            $table->string('status');
            $table->unsignedBigInteger('ID_user');
            $table->foreign('ID_user')->references('ID_user')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('ID_toko');
            $table->foreign('ID_toko')->references('ID_toko')->on('tokos')->onDelete('cascade');
            $table->unsignedBigInteger('ID_keranjang');
            $table->foreign('ID_keranjang')->references('ID_keranjang')->on('carts')->onDelete('cascade');
            $table->string('payment_method');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
