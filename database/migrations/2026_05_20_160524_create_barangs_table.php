<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('barcode', 100)->nullable()->unique();
            $table->string('nama');
            $table->foreignId('kategori_id')->constrained('kategoris')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('satuan', 20)->default('pcs');
            $table->unsignedBigInteger('harga_beli')->default(0);
            $table->unsignedBigInteger('harga_jual')->default(0);
            $table->unsignedInteger('stok_min')->default(0);
            $table->unsignedInteger('stok_max')->default(0);
            $table->unsignedInteger('stok_current')->default(0);
            $table->string('foto')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamp('last_in_at')->nullable();
            $table->timestamp('last_out_at')->nullable();
            $table->timestamps();

            $table->index(['aktif', 'kategori_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
