<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_pack_pricing_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('price_module_stock', 12, 2)->default(0);
            $table->decimal('price_module_accounting', 12, 2)->default(0);
            $table->decimal('price_module_cash_closure', 12, 2)->default(0);
            $table->decimal('price_customization', 12, 2)->default(0);
            $table->decimal('price_agencies_1_4', 12, 2)->default(0);
            $table->decimal('price_agencies_5_10', 12, 2)->default(0);
            $table->decimal('price_agencies_11_plus', 12, 2)->default(0);
            $table->decimal('price_employees_1_5', 12, 2)->default(0);
            $table->decimal('price_employees_6_20', 12, 2)->default(0);
            $table->decimal('price_employees_21_plus', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_pack_pricing_settings');
    }
};
