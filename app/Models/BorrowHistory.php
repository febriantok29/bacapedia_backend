<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BorrowHistory extends Model
{
    use HasUuids;

    protected $table = 'h_borrows';

    public $timestamps = false;

    protected $fillable = [
        'borrow_id',
        'status',
        'remarks',
        'created_at',
        'created_by',
    ];

    public function borrow()
    {
        return $this->belongsTo(Borrow::class, 'borrow_id');
    }
}
