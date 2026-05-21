<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_anomalies', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 30);
            $table->foreignId('barang_id')->nullable()->constrained('barangs')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('severity', 10)->default('info');
            $table->string('judul');
            $table->text('detail')->nullable();
            $table->float('score')->default(0);
            $table->boolean('resolved')->default(false);
            $table->timestamps();
            $table->index(['jenis', 'resolved']);
            $table->index(['severity', 'resolved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_anomalies');
    }
};
