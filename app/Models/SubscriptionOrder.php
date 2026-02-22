<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pressing_id',
        'agency_id',
        'employee_id',
        'subscription_contract_id',
        'reference',
        'order_date',
        'pickup_date',
        'items_count',
        'notes',
        'status',
    ];

    protected $casts = [
        'order_date' => 'date',
        'pickup_date' => 'date',
    ];

    public function contract()
    {
        return $this->belongsTo(SubscriptionContract::class, 'subscription_contract_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
