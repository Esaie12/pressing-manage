<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->boolean('module_stock_enabled')->default(false)->after('module_accounting_enabled');
            $table->string('stock_mode', 20)->nullable()->after('module_stock_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->dropColumn(['module_stock_enabled', 'stock_mode']);
        });
    }
};
