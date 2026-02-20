<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounting_setups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('capital', 12, 2)->default(0);
            $table->decimal('reserves', 12, 2)->default(0);
            $table->decimal('retained_earnings', 12, 2)->default(0);
            $table->decimal('intangible_assets', 12, 2)->default(0);
            $table->decimal('tangible_assets', 12, 2)->default(0);
            $table->decimal('financial_assets', 12, 2)->default(0);
            $table->decimal('stocks', 12, 2)->default(0);
            $table->decimal('receivables', 12, 2)->default(0);
            $table->decimal('treasury', 12, 2)->default(0);
            $table->decimal('financial_debts', 12, 2)->default(0);
            $table->decimal('operating_debts', 12, 2)->default(0);
            $table->decimal('fixed_asset_debts', 12, 2)->default(0);
            $table->decimal('other_debts', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['pressing_id', 'agency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_setups');
    }
};

