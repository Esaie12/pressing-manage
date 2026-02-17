<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = ['name', 'monthly_price', 'annual_price', 'max_agencies', 'max_employees'];
}
