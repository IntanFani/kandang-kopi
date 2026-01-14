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
        Schema::create('stok', function (Blueprint $table) {
            $table->id('id_stok'); // Primary Key kustom
            $table->string('bahan_baku');
            $table->date('tanggal_masuk');
            $table->date('expired')->nullable();
            $table->integer('jumlah');
            $table->string('satuan');
            $table->integer('harga');
            // Tanpa $table->timestamps() karena disetel false di model
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok');
    }
};
