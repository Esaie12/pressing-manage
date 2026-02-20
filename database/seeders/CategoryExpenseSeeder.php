<?php

namespace Database\Seeders;

use App\Models\CategoryExpense;
use Illuminate\Database\Seeder;

class CategoryExpenseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Loyer', 'Électricité', 'Eau', 'Transport', 'Salaire', 'Maintenance', 'Consommables', 'Autre'] as $name) {
            CategoryExpense::firstOrCreate(['name' => $name]);
        }
    }
}
