<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $casts = [
        'allow_customization' => 'boolean',
        'allow_cash_closure_module' => 'boolean',
        'allow_accounting_module' => 'boolean',
        'allow_stock_module' => 'boolean',
        'is_custom' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'monthly_price',
        'annual_price',
        'max_agencies',
        'max_employees',
        'allow_customization',
        'allow_cash_closure_module',
        'allow_accounting_module',
        'allow_stock_module',
        'is_custom',
        'pressing_id',
    ];

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }
}
