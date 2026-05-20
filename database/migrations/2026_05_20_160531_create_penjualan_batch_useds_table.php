<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualan_batch_useds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_detail_id')->constrained('penjualan_details')->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained('product_batches')->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->timestamps();

            $table->index('batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_batch_useds');
    }
};
