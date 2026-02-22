<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->boolean('is_custom')->default(false)->after('allow_stock_module');
            $table->foreignId('pressing_id')->nullable()->after('is_custom')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pressing_id');
            $table->dropColumn('is_custom');
        });
    }
};
