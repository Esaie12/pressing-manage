<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashClosure extends Model
{
    protected $fillable = [
        'pressing_id',
        'agency_id',
        'employee_id',
        'closed_by_user_id',
        'closure_date',
        'encaissement_total',
        'paiement_total',
        'net_total',
        'transactions_count',
        'closed_at',
        'note',
    ];

    protected $casts = [
        'closure_date' => 'date',
        'closed_at' => 'datetime',
        'encaissement_total' => 'decimal:2',
        'paiement_total' => 'decimal:2',
        'net_total' => 'decimal:2',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }
}

