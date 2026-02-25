<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pressing_id',
        'name',
        'company_type',
        'contact_person',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    public function contracts()
    {
        return $this->hasMany(SubscriptionContract::class);
    }
}
