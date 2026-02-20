<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('orders')->where('status', 'created')->update(['status' => 'pending']);
        DB::table('orders')->where('status', 'en_attente')->update(['status' => 'pending']);
    }

    public function down(): void
    {
        DB::table('orders')->where('status', 'pending')->update(['status' => 'created']);
    }
};
