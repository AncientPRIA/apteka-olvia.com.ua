<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Review extends Model
{
    protected $dates = [
        'created_at', 'updated_at'
    ];
    const PUBLISHED = 'PUBLISHED';
    public $timestamps = true;

    protected $perPage = 5;

    public function scopePublished(Builder $query)
    {
        return $query->where('status', '=', static::PUBLISHED);
    }

    public function productId()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
