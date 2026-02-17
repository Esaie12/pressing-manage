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
        'happened_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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
}
