<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['order_id', 'pressing_id', 'invoice_number', 'amount', 'issued_at'];

    protected $casts = [
        'issued_at' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }
}
