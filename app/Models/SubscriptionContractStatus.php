<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionContractStatus extends Model
{
    protected $fillable = ['code', 'label', 'badge_class', 'sort_order'];
}
