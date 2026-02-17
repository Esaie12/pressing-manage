<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SubscriptionPlanSeeder::class,
            CategoryExpenseSeeder::class,
            PressingDemoSeeder::class,
        ]);
    }
}
