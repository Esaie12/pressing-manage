<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('landings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->constrained('pressings')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name')->nullable();
            $table->string('tagline')->nullable();
            $table->string('primary_color', 20)->default('#0d6efd');
            $table->string('secondary_color', 20)->default('#20c997');
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('template_key', 40)->default('minimal_clean');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('hero_title')->nullable();
            $table->string('hero_subtitle')->nullable();
            $table->string('about_title')->nullable();
            $table->text('about_body')->nullable();
            $table->string('contact_title')->nullable();
            $table->string('footer_text')->nullable();
            $table->timestamps();

            $table->unique('pressing_id');
        });

        Schema::create('landing_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_id')->constrained('landings')->cascadeOnDelete();
            $table->string('section_key', 40);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('position')->default(1);
            $table->json('content_json')->nullable();
            $table->timestamps();

            $table->unique(['landing_id', 'section_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_sections');
        Schema::dropIfExists('landings');
    }
};
