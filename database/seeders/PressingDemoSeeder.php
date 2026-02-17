<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\OwnerSubscription;
use App\Models\Pressing;
use App\Models\Service;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PressingDemoSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::updateOrCreate(
            ['email' => 'owner@pressing.local'],
            [
                'name' => 'Propriétaire Démo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
            ]
        );

        $pressing = Pressing::updateOrCreate(
            ['name' => 'Pressing Excellence'],
            ['owner_id' => $owner->id, 'phone' => '770000001']
        );

        $owner->update(['pressing_id' => $pressing->id]);

        $agency = Agency::updateOrCreate(
            ['name' => 'Agence Plateau', 'pressing_id' => $pressing->id],
            ['address' => 'Dakar Plateau']
        );

        User::updateOrCreate(
            ['email' => 'employee@pressing.local'],
            [
                'name' => 'Employé Démo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_EMPLOYEE,
                'pressing_id' => $pressing->id,
                'agency_id' => $agency->id,
            ]
        );

        Service::updateOrCreate(
            ['name' => 'Lavage classique', 'agency_id' => $agency->id],
            ['price' => 2500, 'description' => 'Lavage et séchage standard']
        );

        $plan = SubscriptionPlan::where('name', 'Pro')->first();
        if ($plan) {
            OwnerSubscription::updateOrCreate(
                ['pressing_id' => $pressing->id, 'subscription_plan_id' => $plan->id],
                [
                    'billing_cycle' => 'monthly',
                    'starts_at' => now()->startOfMonth()->toDateString(),
                    'ends_at' => now()->endOfMonth()->toDateString(),
                    'is_active' => true,
                ]
            );
        }
    }
}
