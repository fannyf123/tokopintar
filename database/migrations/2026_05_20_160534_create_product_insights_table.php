<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->unique()->constrained('barangs')->cascadeOnDelete();
            $table->decimal('velocity_30', 10, 4)->default(0);
            $table->decimal('days_of_supply', 10, 2)->default(0);
            $table->string('kelas', 20)->default('NEW');
            $table->string('abc_class', 1)->nullable();
            $table->decimal('forecast_7', 10, 4)->default(0);
            $table->string('rekomendasi_text')->nullable();
            $table->timestamp('dihitung_pada')->nullable();
            $table->timestamps();

            $table->index('kelas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_insights');
    }
};
