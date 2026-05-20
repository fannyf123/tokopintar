<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->string('kategori', 50);
            $table->date('tanggal');
            $table->unsignedBigInteger('jumlah');
            $table->string('catatan')->nullable();
            $table->string('bukti')->nullable();
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
