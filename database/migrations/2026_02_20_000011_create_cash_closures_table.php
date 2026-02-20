<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->date('closure_date');
            $table->decimal('encaissement_total', 10, 2)->default(0);
            $table->decimal('paiement_total', 10, 2)->default(0);
            $table->decimal('net_total', 10, 2)->default(0);
            $table->unsignedInteger('transactions_count')->default(0);
            $table->timestamp('closed_at');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['pressing_id', 'closure_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_closures');
    }
};

