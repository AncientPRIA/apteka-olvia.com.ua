<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class City extends Model
{
    public $timestamps = false;


    

    public function shops(){
        return $this->hasMany(ShopLocation::class);
    }

}
