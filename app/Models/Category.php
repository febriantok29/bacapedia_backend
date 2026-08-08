<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'm_categories';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];

    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }
}
