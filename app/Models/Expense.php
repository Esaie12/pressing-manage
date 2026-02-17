<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pressing_id',
        'agency_id',
        'title',
        'category',
        'amount',
        'expense_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
