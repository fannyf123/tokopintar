<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelians')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barangs')->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('harga_beli');
            $table->unsignedBigInteger('subtotal');
            $table->string('no_batch', 50)->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelian_details');
    }
};
