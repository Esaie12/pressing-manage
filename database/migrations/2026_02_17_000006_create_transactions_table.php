<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('expense_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['encaissement', 'paiement']);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('label')->nullable();
            $table->timestamp('happened_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
