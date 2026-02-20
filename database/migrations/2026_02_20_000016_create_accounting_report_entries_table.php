<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounting_report_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('entry_type', 20);
            $table->decimal('amount', 12, 2);
            $table->string('payment_method')->nullable();
            $table->string('label')->nullable();
            $table->string('order_reference')->nullable();
            $table->timestamp('happened_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_report_entries');
    }
};

