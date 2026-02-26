<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->boolean('module_landing_enabled')->default(false)->after('module_subscription_enabled');
        });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->boolean('allow_landing_module')->default(true)->after('allow_subscription_module');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('allow_landing_module');
        });

        Schema::table('pressings', function (Blueprint $table) {
            $table->dropColumn('module_landing_enabled');
        });
    }
};
