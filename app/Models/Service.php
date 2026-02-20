<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = ['agency_id', 'name', 'price', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
