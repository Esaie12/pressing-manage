<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    protected $fillable = [
        'pressing_id',
        'stock_item_id',
        'agency_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
