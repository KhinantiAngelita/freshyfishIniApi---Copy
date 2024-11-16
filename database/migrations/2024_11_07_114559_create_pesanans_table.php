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
            $table->integer('order_quantity');
            $table->decimal('total_price', 10, 2);
            $table->dateTime('order_date');
            $table->string('status');
            $table->unsignedBigInteger('ID_user');
            $table->unsignedBigInteger('ID_keranjang');
            $table->string('payment_method');
            $table->timestamps();

            // Foreign keys
            $table->foreign('ID_user')->references('ID_user')->on('users')->onDelete('cascade');
            $table->foreign('ID_keranjang')->references('ID_keranjang')->on('carts')->onDelete('cascade');
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
