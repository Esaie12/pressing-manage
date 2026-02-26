<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pressing_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('invoice_template')->default('classic');
            $table->string('invoice_primary_color')->default('#0d6efd');
            $table->string('invoice_welcome_message')->nullable();
            $table->string('invoice_logo_path')->nullable();
            $table->string('invoice_reference_mode')->default('random');
            $table->string('invoice_reference_separator')->default('-');
            $table->json('invoice_reference_parts')->nullable();
            $table->timestamps();
        });

        $pressings = DB::table('pressings')->select([
            'id',
            'invoice_template',
            'invoice_primary_color',
            'invoice_welcome_message',
            'invoice_logo_path',
            'invoice_reference_mode',
            'invoice_reference_separator',
            'invoice_reference_parts',
        ])->get();

        $now = now();
        foreach ($pressings as $pressing) {
            DB::table('invoice_settings')->insert([
                'pressing_id' => $pressing->id,
                'invoice_template' => $pressing->invoice_template ?? 'classic',
                'invoice_primary_color' => $pressing->invoice_primary_color ?? '#0d6efd',
                'invoice_welcome_message' => $pressing->invoice_welcome_message,
                'invoice_logo_path' => $pressing->invoice_logo_path,
                'invoice_reference_mode' => $pressing->invoice_reference_mode ?? 'random',
                'invoice_reference_separator' => $pressing->invoice_reference_separator ?? '-',
                'invoice_reference_parts' => $pressing->invoice_reference_parts,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::table('pressings', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_template',
                'invoice_primary_color',
                'invoice_welcome_message',
                'invoice_logo_path',
                'invoice_reference_mode',
                'invoice_reference_separator',
                'invoice_reference_parts',
                'invoice_reference_locked',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('pressings', function (Blueprint $table) {
            $table->string('invoice_template')->default('classic')->after('address');
            $table->string('invoice_primary_color')->default('#0d6efd')->after('invoice_template');
            $table->string('invoice_welcome_message')->nullable()->after('invoice_primary_color');
            $table->string('invoice_logo_path')->nullable()->after('invoice_welcome_message');
            $table->string('invoice_reference_mode')->default('random')->after('invoice_logo_path');
            $table->string('invoice_reference_separator')->default('-')->after('invoice_reference_mode');
            $table->json('invoice_reference_parts')->nullable()->after('invoice_reference_separator');
            $table->boolean('invoice_reference_locked')->default(false)->after('invoice_reference_parts');
        });

        $settings = DB::table('invoice_settings')->get();
        foreach ($settings as $setting) {
            DB::table('pressings')
                ->where('id', $setting->pressing_id)
                ->update([
                    'invoice_template' => $setting->invoice_template,
                    'invoice_primary_color' => $setting->invoice_primary_color,
                    'invoice_welcome_message' => $setting->invoice_welcome_message,
                    'invoice_logo_path' => $setting->invoice_logo_path,
                    'invoice_reference_mode' => $setting->invoice_reference_mode,
                    'invoice_reference_separator' => $setting->invoice_reference_separator,
                    'invoice_reference_parts' => $setting->invoice_reference_parts,
                    'invoice_reference_locked' => false,
                ]);
        }

        Schema::dropIfExists('invoice_settings');
    }
};
