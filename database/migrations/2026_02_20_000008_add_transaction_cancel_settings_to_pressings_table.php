<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->boolean('allow_transaction_cancellation')->default(false)->after('closing_time');
            $table->unsignedInteger('transaction_cancellation_window_minutes')->nullable()->after('allow_transaction_cancellation');
        });
    }

    public function down(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->dropColumn(['allow_transaction_cancellation', 'transaction_cancellation_window_minutes']);
        });
    }
};

