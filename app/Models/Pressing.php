<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pressing extends Model
{
    protected $fillable = [
        'name', 'owner_id', 'phone', 'address',
        'invoice_template', 'invoice_primary_color', 'invoice_welcome_message', 'invoice_logo_path', 'opening_time', 'closing_time'
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
}
