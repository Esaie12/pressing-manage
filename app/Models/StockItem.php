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
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }
}
