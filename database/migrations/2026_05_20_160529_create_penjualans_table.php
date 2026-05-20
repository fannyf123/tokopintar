<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->dateTime('tanggal');
            $table->foreignId('kasir_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggans')->nullOnDelete();
            $table->unsignedBigInteger('total')->default(0);
            $table->unsignedBigInteger('diskon')->default(0);
            $table->unsignedBigInteger('pajak')->default(0);
            $table->unsignedBigInteger('grand_total')->default(0);
            $table->unsignedBigInteger('dibayar')->default(0);
            $table->unsignedBigInteger('kembalian')->default(0);
            $table->string('metode_bayar', 20)->default('cash');
            $table->string('status', 20)->default('lunas');
            $table->timestamps();

            $table->index(['tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
