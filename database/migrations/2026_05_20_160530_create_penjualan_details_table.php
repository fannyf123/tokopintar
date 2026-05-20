<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualans')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barangs')->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('harga_jual_saat_itu');
            $table->unsignedBigInteger('diskon_item')->default(0);
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('hpp_saat_itu')->default(0);
            $table->timestamps();

            $table->index('barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};
