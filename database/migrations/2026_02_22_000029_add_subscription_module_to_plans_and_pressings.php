<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->boolean('module_subscription_enabled')->default(false)->after('module_stock_enabled');
        });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->boolean('allow_subscription_module')->default(true)->after('allow_stock_module');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('allow_subscription_module');
        });

        Schema::table('pressings', function (Blueprint $table) {
            $table->dropColumn('module_subscription_enabled');
        });
    }
};
