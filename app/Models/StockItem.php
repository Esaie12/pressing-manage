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

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'stock_item_supplier', 'stock_item_id', 'supplier_id')->withTimestamps();
    }

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }
}
