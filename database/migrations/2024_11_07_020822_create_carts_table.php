<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id('ID_keranjang');
            $table->integer('order_quantity');
            $table->unsignedBigInteger('ID_user');
            $table->unsignedBigInteger('ID_produk');
            $table->timestamps();

            // Foreign keys
            $table->foreign('ID_user')->references('ID_user')->on('users')->onDelete('cascade');
            $table->foreign('ID_produk')->references('ID_produk')->on('produk')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
