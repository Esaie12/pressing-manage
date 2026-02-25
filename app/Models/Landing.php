<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Landing extends Model
{
    protected $fillable = [
        'pressing_id',
        'slug',
        'name',
        'tagline',
        'primary_color',
        'secondary_color',
        'whatsapp_number',
        'contact_email',
        'template_key',
        'status',
        'meta_title',
        'meta_description',
        'hero_title',
        'hero_subtitle',
        'about_title',
        'about_body',
        'contact_title',
        'footer_text',
    ];

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }

    public function sections()
    {
        return $this->hasMany(LandingSection::class)->orderBy('position');
    }
}
