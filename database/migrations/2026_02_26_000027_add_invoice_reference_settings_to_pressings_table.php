<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->string('invoice_reference_mode')->default('random')->after('invoice_logo_path');
            $table->string('invoice_reference_separator')->default('-')->after('invoice_reference_mode');
            $table->json('invoice_reference_parts')->nullable()->after('invoice_reference_separator');
            $table->boolean('invoice_reference_locked')->default(false)->after('invoice_reference_parts');
        });
    }

    public function down(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_reference_mode',
                'invoice_reference_separator',
                'invoice_reference_parts',
                'invoice_reference_locked',
            ]);
        });
    }
};
