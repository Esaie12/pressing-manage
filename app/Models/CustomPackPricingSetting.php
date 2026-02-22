<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomPackPricingSetting extends Model
{
    protected $fillable = [
        'base_price',
        'price_module_stock',
        'price_module_accounting',
        'price_module_cash_closure',
        'price_customization',
        'price_agencies_1_4',
        'price_agencies_5_10',
        'price_agencies_11_plus',
        'price_employees_1_5',
        'price_employees_6_20',
        'price_employees_21_plus',
    ];
}
