<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->decimal('alert_quantity_central', 12, 2)->nullable()->after('unit');
            $table->decimal('alert_quantity_agency', 12, 2)->nullable()->after('alert_quantity_central');
        });
    }

    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropColumn(['alert_quantity_central', 'alert_quantity_agency']);
        });
    }
};
