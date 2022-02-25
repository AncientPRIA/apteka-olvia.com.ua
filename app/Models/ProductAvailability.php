<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class ProductAvailability extends Model
{

    public $table = "product_availability";

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function shop(){
        return $this->belongsTo(ShopLocation::class, 'shop_location_id', 'id');
    }

    public function scopeIn_shop(Builder $query, $shop_location_id)
    {
        return $query->where('shop_location_id', '=', $shop_location_id);
    }

    public function scopeIn_product(Builder $query, $product_id)
    {
        return $query->where('product_id', '=', $product_id);
    }

    
}
