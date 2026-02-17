<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => 'Starter', 'monthly_price' => 15000, 'annual_price' => 150000, 'max_agencies' => 1, 'max_employees' => 5],
            ['name' => 'Pro', 'monthly_price' => 30000, 'annual_price' => 300000, 'max_agencies' => 5, 'max_employees' => 20],
            ['name' => 'Enterprise', 'monthly_price' => 60000, 'annual_price' => 600000, 'max_agencies' => 50, 'max_employees' => 200],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
