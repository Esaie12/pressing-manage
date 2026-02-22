<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('stock_item_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['stock_item_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_item_supplier');
        Schema::dropIfExists('suppliers');
    }
};
