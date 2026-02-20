<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomPackRequest extends Model
{
    protected $fillable = [
        'pressing_id',
        'requested_agencies',
        'requested_employees',
        'want_stock_module',
        'want_accounting_module',
        'want_cash_closure_module',
        'want_customization',
        'estimated_price',
        'note',
        'status',
    ];

    protected $casts = [
        'want_stock_module' => 'boolean',
        'want_accounting_module' => 'boolean',
        'want_cash_closure_module' => 'boolean',
        'want_customization' => 'boolean',
        'estimated_price' => 'decimal:2',
    ];

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }
}
