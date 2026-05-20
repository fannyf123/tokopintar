<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->string('no_batch', 50)->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->unsignedInteger('qty_awal');
            $table->unsignedInteger('qty_sisa');
            $table->unsignedBigInteger('harga_beli_batch');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->timestamps();

            $table->index(['barang_id', 'tanggal_kadaluarsa']);
            $table->index(['barang_id', 'qty_sisa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
