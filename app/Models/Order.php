<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'agency_id', 'client_id', 'employee_id', 'reference', 'total',
        'status', 'paid_advance', 'advance_amount', 'payment_method', 'is_delivery', 'delivery_address', 'delivery_fee', 'picked_up_at', 'ready_at'
    ];

    protected $casts = [
        'paid_advance' => 'boolean',
        'advance_amount' => 'decimal:2',
        'is_delivery' => 'boolean',
        'delivery_fee' => 'decimal:2',
        'picked_up_at' => 'datetime',
        'ready_at' => 'datetime',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
