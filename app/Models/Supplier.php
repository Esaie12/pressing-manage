<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'pressing_id',
        'name',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->belongsToMany(StockItem::class, 'stock_item_supplier', 'supplier_id', 'stock_item_id')->withTimestamps();
    }
}
