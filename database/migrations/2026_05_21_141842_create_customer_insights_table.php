<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')->unique()->constrained('pelanggans')->cascadeOnDelete();
            $table->unsignedInteger('recency_days')->default(0);
            $table->unsignedInteger('frequency')->default(0);
            $table->unsignedBigInteger('monetary')->default(0);
            $table->unsignedTinyInteger('r_score')->default(1);
            $table->unsignedTinyInteger('f_score')->default(1);
            $table->unsignedTinyInteger('m_score')->default(1);
            $table->string('segment', 30)->default('NEW');
            $table->unsignedBigInteger('clv_estimate')->default(0);
            $table->float('avg_interval_days')->default(0);
            $table->boolean('churn_risk')->default(false);
            $table->string('rekomendasi_text')->nullable();
            $table->timestamp('dihitung_pada')->nullable();
            $table->timestamps();
            $table->index('segment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_insights');
    }
};
