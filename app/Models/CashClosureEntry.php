<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashClosureEntry extends Model
{
    protected $fillable = [
        'cash_closure_id',
        'transaction_id',
        'user_id',
        'transaction_type',
        'amount',
        'payment_method',
        'label',
        'order_reference',
        'happened_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'happened_at' => 'datetime',
    ];

    public function cashClosure()
    {
        return $this->belongsTo(CashClosure::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

