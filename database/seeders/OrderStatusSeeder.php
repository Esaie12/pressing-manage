<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'pending', 'label' => 'Attente', 'badge_class' => 'warning', 'sort_order' => 1],
            ['code' => 'ready', 'label' => 'Prête', 'badge_class' => 'primary', 'sort_order' => 2],
            ['code' => 'picked_up', 'label' => 'Retirée', 'badge_class' => 'success', 'sort_order' => 3],
        ];

        foreach ($statuses as $status) {
            OrderStatus::updateOrCreate(['code' => $status['code']], $status);
        }
    }
}
