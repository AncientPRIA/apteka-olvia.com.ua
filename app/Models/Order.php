<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Order extends Model
{

    public $timestamps = true;

    protected $casts = [
        //'image' => 'array',
    ];

    public function items(){
        return $this->hasMany(OrdersItem::class, 'order_id', 'id');
    }

    //public $fill


}
