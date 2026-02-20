<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accounting_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('accounting_setup_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('month');
            $table->decimal('total_credits', 12, 2)->default(0);
            $table->decimal('total_debits', 12, 2)->default(0);
            $table->decimal('net_result', 12, 2)->default(0);
            $table->json('snapshot')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('saved_at');
            $table->timestamps();

            $table->index(['pressing_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_reports');
    }
};

