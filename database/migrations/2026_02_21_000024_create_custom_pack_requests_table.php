<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_pack_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('requested_agencies')->default(1);
            $table->unsignedInteger('requested_employees')->default(1);
            $table->boolean('want_stock_module')->default(false);
            $table->boolean('want_accounting_module')->default(false);
            $table->boolean('want_cash_closure_module')->default(false);
            $table->boolean('want_customization')->default(false);
            $table->decimal('estimated_price', 12, 2)->default(0);
            $table->string('status', 20)->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_pack_requests');
    }
};
