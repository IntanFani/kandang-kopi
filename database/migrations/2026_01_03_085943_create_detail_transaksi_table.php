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
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi');
            $table->unsignedBigInteger('id_menu');
            $table->integer('quantity');
            $table->integer('harga_satuan');
            $table->integer('subtotal');

            // Relasi ke tabel transaksi & menus
            $table->foreign('kode_transaksi')->references('kode_transaksi')->on('transaksi')->onDelete('cascade');
            $table->foreign('id_menu')->references('id')->on('menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi');
    }
};
