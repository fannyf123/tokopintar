<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('barang_a_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('barang_b_id')->constrained('barangs')->cascadeOnDelete();
            $table->unsignedBigInteger('harga_bundle');
            $table->unsignedBigInteger('harga_normal');
            $table->unsignedBigInteger('saving');
            $table->float('total_margin_pct')->default(0);
            $table->float('lift_score')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->index('aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundles');
    }
};
