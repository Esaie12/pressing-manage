<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pressing_id',
        'subscription_client_id',
        'title',
        'starts_at',
        'ends_at',
        'frequency',
        'price',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(SubscriptionClient::class, 'subscription_client_id');
    }
}
