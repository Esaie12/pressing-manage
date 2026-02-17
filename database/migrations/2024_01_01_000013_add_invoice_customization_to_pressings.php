<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->string('invoice_template')->default('classic')->after('address');
            $table->string('invoice_primary_color')->default('#0d6efd')->after('invoice_template');
            $table->string('invoice_welcome_message')->nullable()->after('invoice_primary_color');
        });
    }

    public function down(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->dropColumn(['invoice_template', 'invoice_primary_color', 'invoice_welcome_message']);
        });
    }
};
