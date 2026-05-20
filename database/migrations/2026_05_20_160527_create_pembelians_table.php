<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 30)->unique();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->unsignedBigInteger('total')->default(0);
            $table->unsignedBigInteger('dibayar')->default(0);
            $table->string('metode_bayar', 20)->default('cash');
            $table->string('status', 20)->default('draft');
            $table->string('catatan')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tanggal', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
