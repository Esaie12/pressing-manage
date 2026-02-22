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
        'frequency_interval',
        'frequency_unit',
        'price',
        'notes',
        'is_active',
        'subscription_contract_status_id',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'price' => 'decimal:2',
        'frequency_interval' => 'integer',
        'is_active' => 'boolean',
    ];

    public function status()
    {
        return $this->belongsTo(SubscriptionContractStatus::class, 'subscription_contract_status_id');
    }

    public function client()
    {
        return $this->belongsTo(SubscriptionClient::class, 'subscription_client_id');
    }
}
