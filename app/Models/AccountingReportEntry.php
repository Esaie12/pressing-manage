<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingReportEntry extends Model
{
    protected $fillable = [
        'accounting_report_id',
        'transaction_id',
        'agency_id',
        'user_id',
        'entry_type',
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

    public function report()
    {
        return $this->belongsTo(AccountingReport::class, 'accounting_report_id');
    }
}
