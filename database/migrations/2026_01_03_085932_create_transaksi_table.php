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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->unsignedBigInteger('id_kasir');
            $table->dateTime('tgl_transaksi');
            $table->decimal('total_bayar', 15, 2);
            $table->integer('jumlah_bayar');
            $table->integer('kembalian');
            
            // Relasi ke tabel users
            $table->foreign('id_kasir')->references('id')->on('users')->onDelete('cascade');
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
