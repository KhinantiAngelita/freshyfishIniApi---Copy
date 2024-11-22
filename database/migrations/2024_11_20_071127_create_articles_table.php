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
        Schema::create('articles', function (Blueprint $table) {
            $table->id('ID_article');
            $table->unsignedBigInteger('ID_user');
            $table->string('title');
            $table->string('category_content');
            $table->text('content');
            $table->string('photo_content')->nullable(); // Kolom photo_content
            $table->timestamps();
            
            $table->foreign('ID_user')->references('ID_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
