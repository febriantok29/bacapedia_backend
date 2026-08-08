<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Config extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 's_config';

    protected $fillable = [
        'key',
        'value',
        'active_start_date',
        'active_end_date',
    ];

    protected $casts = [
        'active_start_date' => 'date',
        'active_end_date' => 'date',
    ];
}
