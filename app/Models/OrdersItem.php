<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrdersItem extends Model
{
    public $timestamps = true;

    public function item(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
