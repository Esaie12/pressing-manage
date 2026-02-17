<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['agency_id', 'name', 'price', 'description'];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
}
