<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscription_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('company_type')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('subscription_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_client_id')->constrained('subscription_clients')->cascadeOnDelete();
            $table->string('title');
            $table->date('starts_at');
            $table->date('ends_at')->nullable();
            $table->enum('frequency', ['day', 'week', 'month']);
            $table->decimal('price', 12, 2);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('subscription_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('subscription_contract_id')->constrained('subscription_contracts')->cascadeOnDelete();
            $table->string('reference')->unique();
            $table->date('order_date');
            $table->date('pickup_date')->nullable();
            $table->unsignedInteger('items_count')->default(1);
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'ready', 'delivered'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_orders');
        Schema::dropIfExists('subscription_contracts');
        Schema::dropIfExists('subscription_clients');
    }
};
