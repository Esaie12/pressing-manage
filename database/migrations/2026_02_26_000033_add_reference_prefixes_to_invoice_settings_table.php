<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->string('invoice_order_reference_prefix', 3)->default('CMD')->after('invoice_reference_parts');
            $table->string('invoice_invoice_reference_prefix', 3)->default('FAC')->after('invoice_order_reference_prefix');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_settings', function (Blueprint $table) {
            $table->dropColumn(['invoice_order_reference_prefix', 'invoice_invoice_reference_prefix']);
        });
    }
};
