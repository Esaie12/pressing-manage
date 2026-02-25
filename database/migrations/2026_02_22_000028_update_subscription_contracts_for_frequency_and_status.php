<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscription_contracts', function (Blueprint $table) {
            $table->unsignedSmallInteger('frequency_interval')->default(1)->after('ends_at');
            $table->enum('frequency_unit', ['day', 'week', 'month'])->default('week')->after('frequency_interval');
            $table->foreignId('subscription_contract_status_id')->nullable()->after('notes')->constrained('subscription_contract_statuses')->nullOnDelete();
        });

        DB::table('subscription_contracts')->update([
            'frequency_interval' => 1,
            'frequency_unit' => DB::raw("CASE frequency WHEN 'day' THEN 'day' WHEN 'month' THEN 'month' ELSE 'week' END"),
        ]);

        $activeId = DB::table('subscription_contract_statuses')->where('code', 'active')->value('id');
        if ($activeId) {
            DB::table('subscription_contracts')->update(['subscription_contract_status_id' => $activeId]);
        }
    }

    public function down(): void
    {
        Schema::table('subscription_contracts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subscription_contract_status_id');
            $table->dropColumn(['frequency_interval', 'frequency_unit']);
        });
    }
};
