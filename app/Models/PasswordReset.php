<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasUuids;

    protected $table = 's_password_resets';

    public $timestamps = false;

    protected $fillable = [
        'guard',
        'user_id',
        'token',
        'used',
        'expires_at',
        'created_at',
    ];

    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
    ];
}
