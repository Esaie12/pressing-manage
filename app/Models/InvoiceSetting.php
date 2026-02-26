<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $fillable = [
        'pressing_id',
        'invoice_template',
        'invoice_primary_color',
        'invoice_welcome_message',
        'invoice_logo_path',
        'invoice_reference_mode',
        'invoice_reference_separator',
        'invoice_reference_parts',
    ];

    protected $casts = [
        'invoice_reference_parts' => 'array',
    ];

    public function pressing()
    {
        return $this->belongsTo(Pressing::class);
    }
}
