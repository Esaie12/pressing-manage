<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerSubscription extends Model
{
    protected $fillable = [
        'pressing_id', 'subscription_plan_id', 'billing_cycle', 'starts_at', 'ends_at', 'is_active'
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
