<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('source_agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->foreignId('target_agency_id')->nullable()->constrained('agencies')->nullOnDelete();
            $table->string('movement_type', 30); // entree, sortie, transfert, ajustement, perte_casse
            $table->decimal('quantity', 12, 2);
            $table->text('note')->nullable();
            $table->date('movement_date');
            $table->timestamps();
        });

        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['pressing_id', 'stock_item_id', 'agency_id'], 'stock_balances_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
        Schema::dropIfExists('stock_movements');
    }
};
