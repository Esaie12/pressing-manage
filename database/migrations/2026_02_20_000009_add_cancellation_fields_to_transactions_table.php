<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_cancelled')->default(false)->after('label');
            $table->foreignId('cancelled_by_user_id')->nullable()->after('is_cancelled')->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable()->after('cancelled_by_user_id');
            $table->text('cancellation_note')->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cancelled_by_user_id');
            $table->dropColumn(['is_cancelled', 'cancelled_at', 'cancellation_note']);
        });
    }
};

