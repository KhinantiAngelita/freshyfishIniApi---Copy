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
        Schema::create('produk', function (Blueprint $table) {
            $table->id('ID_produk'); // Primary Key
            $table->string('fish_type');
            $table->decimal('fish_price', 10, 2);
            $table->decimal('fish_weight', 8, 2);
            $table->string('fish_photo')->nullable();
            $table->string('fish_description');
            $table->string('habitat');
            $table->unsignedBigInteger('ID_toko'); // Foreign Key
            $table->foreign('ID_toko')->references('ID_toko')->on('tokos')->onDelete('cascade'); // Relasi ke tabel Toko
            $table->timestamps(); // Timestamp untuk created_at dan updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('produk');
    }
};
