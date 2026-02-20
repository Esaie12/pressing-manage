<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'pressing_id',
        'stock_item_id',
        'user_id',
        'agency_id',
        'source_agency_id',
        'target_agency_id',
        'movement_type',
        'quantity',
        'note',
        'movement_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'movement_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function sourceAgency()
    {
        return $this->belongsTo(Agency::class, 'source_agency_id');
    }

    public function targetAgency()
    {
        return $this->belongsTo(Agency::class, 'target_agency_id');
    }
}
