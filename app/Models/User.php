<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 's_users';

    protected $fillable = [
        'user_code',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'deleted_at',
        'deleted_by',
    ];

    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'user_id');
    }
}
