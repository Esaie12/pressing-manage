<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingSetup extends Model
{
    protected $fillable = [
        'pressing_id',
        'agency_id',
        'capital',
        'reserves',
        'retained_earnings',
        'intangible_assets',
        'tangible_assets',
        'financial_assets',
        'stocks',
        'receivables',
        'treasury',
        'financial_debts',
        'operating_debts',
        'fixed_asset_debts',
        'other_debts',
        'notes',
    ];

    protected $casts = [
        'capital' => 'decimal:2',
        'reserves' => 'decimal:2',
        'retained_earnings' => 'decimal:2',
        'intangible_assets' => 'decimal:2',
        'tangible_assets' => 'decimal:2',
        'financial_assets' => 'decimal:2',
        'stocks' => 'decimal:2',
        'receivables' => 'decimal:2',
        'treasury' => 'decimal:2',
        'financial_debts' => 'decimal:2',
        'operating_debts' => 'decimal:2',
        'fixed_asset_debts' => 'decimal:2',
        'other_debts' => 'decimal:2',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
