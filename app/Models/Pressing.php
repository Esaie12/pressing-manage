<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pressing extends Model
{
    protected $fillable = [
        'name', 'owner_id', 'phone', 'address',
        'opening_time', 'closing_time',
        'allow_transaction_cancellation', 'transaction_cancellation_window_minutes',
        'module_cash_closure_enabled',
        'module_accounting_enabled',
        'module_stock_enabled',
        'module_subscription_enabled',
        'stock_mode',
    ];

    protected $casts = [
        'allow_transaction_cancellation' => 'boolean',
        'module_cash_closure_enabled' => 'boolean',
        'module_accounting_enabled' => 'boolean',
        'module_stock_enabled' => 'boolean',
        'module_subscription_enabled' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function agencies()
    {
        return $this->hasMany(Agency::class);
    }

    public function employees()
    {
        return $this->hasMany(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }


    public function invoiceSetting()
    {
        return $this->hasOne(InvoiceSetting::class);
    }

    public function cashClosures()
    {
        return $this->hasMany(CashClosure::class);
    }
}
