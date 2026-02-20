<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->boolean('allow_customization')->default(true)->after('max_employees');
            $table->boolean('allow_cash_closure_module')->default(true)->after('allow_customization');
            $table->boolean('allow_accounting_module')->default(true)->after('allow_cash_closure_module');
            $table->boolean('allow_stock_module')->default(true)->after('allow_accounting_module');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn([
                'allow_customization',
                'allow_cash_closure_module',
                'allow_accounting_module',
                'allow_stock_module',
            ]);
        });
    }
};
