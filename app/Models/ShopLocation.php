<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ShopLocation extends Model
{

    public function city(){
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

//    public function availability(){
//        return $this->belongsTo(ProductAvailability::class, 'shop_location_id', 'id');
//    }
}
