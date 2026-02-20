<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingReport extends Model
{
    protected $fillable = [
        'pressing_id',
        'agency_id',
        'accounting_setup_id',
        'created_by_user_id',
        'month',
        'total_credits',
        'total_debits',
        'net_result',
        'snapshot',
        'note',
        'saved_at',
    ];

    protected $casts = [
        'month' => 'date',
        'total_credits' => 'decimal:2',
        'total_debits' => 'decimal:2',
        'net_result' => 'decimal:2',
        'snapshot' => 'array',
        'saved_at' => 'datetime',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function setup()
    {
        return $this->belongsTo(AccountingSetup::class, 'accounting_setup_id');
    }

    public function entries()
    {
        return $this->hasMany(AccountingReportEntry::class);
    }
}
