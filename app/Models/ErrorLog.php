<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    use HasUuids;

    protected $table = 's_error_logs';

    public $timestamps = false;

    protected $fillable = [
        'error_code',
        'message',
        'stack_trace',
        'user_id',
        'endpoint',
        'http_method',
        'request_body',
        'created_at',
    ];
}
