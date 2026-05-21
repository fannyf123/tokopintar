<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitor_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->string('competitor_name', 100);
            $table->unsignedBigInteger('harga_competitor');
            $table->date('tanggal_observasi');
            $table->string('catatan')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['barang_id', 'tanggal_observasi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitor_prices');
    }
};
