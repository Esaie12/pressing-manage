<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    protected $fillable = [
        'pressing_id',
        'name',
        'sku',
        'unit',
        'alert_quantity_central',
        'alert_quantity_agency',
        'is_active',
    ];

    protected $casts = [
        'alert_quantity_central' => 'decimal:2',
        'alert_quantity_agency' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }
}
