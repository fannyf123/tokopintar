<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_insights', function (Blueprint $table) {
            $table->decimal('margin_pct', 6, 2)->default(0)->after('forecast_7');
            $table->string('strategy', 20)->nullable()->after('margin_pct');
            $table->string('strategy_partner_ids', 255)->nullable()->after('strategy');
            $table->string('strategy_text', 500)->nullable()->after('strategy_partner_ids');
        });
    }

    public function down(): void
    {
        Schema::table('product_insights', function (Blueprint $table) {
            $table->dropColumn(['margin_pct', 'strategy', 'strategy_partner_ids', 'strategy_text']);
        });
    }
};
