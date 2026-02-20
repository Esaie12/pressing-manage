<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'pressing_id',
        'agency_id',
        'user_id',
        'order_id',
        'expense_id',
        'type',
        'amount',
        'payment_method',
        'label',
        'is_cancelled',
        'cancelled_by_user_id',
        'cancelled_at',
        'cancellation_note',
        'happened_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_cancelled' => 'boolean',
        'cancelled_at' => 'datetime',
        'happened_at' => 'datetime',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by_user_id');
    }
}
