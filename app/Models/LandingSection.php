<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSection extends Model
{
    protected $fillable = [
        'landing_id',
        'section_key',
        'is_visible',
        'position',
        'content_json',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'content_json' => 'array',
    ];

    public function landing()
    {
        return $this->belongsTo(Landing::class);
    }
}
