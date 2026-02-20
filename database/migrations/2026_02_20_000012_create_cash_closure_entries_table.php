<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_closure_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_closure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_type', 30);
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->string('label')->nullable();
            $table->string('order_reference')->nullable();
            $table->timestamp('happened_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_closure_entries');
    }
};

