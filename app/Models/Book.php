<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'm_books';

    protected $fillable = [
        'book_code',
        'category_id',
        'title',
        'author',
        'publisher',
        'published_year',
        'stock',
    ];

    protected $hidden = [
        'deleted_at',
        'deleted_by',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'book_id');
    }
}
