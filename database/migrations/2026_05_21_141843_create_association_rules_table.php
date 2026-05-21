<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('association_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antecedent_barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->foreignId('consequent_barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->float('support')->default(0);
            $table->float('confidence')->default(0);
            $table->float('lift')->default(0);
            $table->unsignedInteger('co_count')->default(0);
            $table->timestamp('dihitung_pada')->nullable();
            $table->timestamps();
            $table->index('antecedent_barang_id');
            $table->index(['lift', 'confidence']);
            $table->unique(['antecedent_barang_id', 'consequent_barang_id'], 'assoc_pair_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('association_rules');
    }
};
