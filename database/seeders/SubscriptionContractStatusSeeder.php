<?php

namespace Database\Seeders;

use App\Models\SubscriptionContractStatus;
use Illuminate\Database\Seeder;

class SubscriptionContractStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'active', 'label' => 'Actif', 'badge_class' => 'success', 'sort_order' => 1],
            ['code' => 'paused', 'label' => 'Suspendu', 'badge_class' => 'warning', 'sort_order' => 2],
            ['code' => 'expired', 'label' => 'Expiré', 'badge_class' => 'secondary', 'sort_order' => 3],
            ['code' => 'terminated', 'label' => 'Résilié', 'badge_class' => 'danger', 'sort_order' => 4],
        ];

        foreach ($statuses as $status) {
            SubscriptionContractStatus::updateOrCreate(['code' => $status['code']], $status);
        }
    }
}
