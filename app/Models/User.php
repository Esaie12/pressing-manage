<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_OWNER = 'owner';
    public const ROLE_EMPLOYEE = 'employee';

    protected $fillable = [
        'name', 'email', 'password', 'role', 'is_active', 'pressing_id', 'agency_id'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
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
