<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Image extends Model
{
    public $timestamps = false;

    /*
    public function get_item($key, $tag = null){
        $result = $this->query()
            ->where('key', '=', $key)
        ;
        if($tag !== null){
            $result = $result->where('tag', '=', $tag);
        }
        $result = $result->first();
    }
    */

    public static function get_item($key, $tag = null){
        $result = self::query()
            ->where('key', '=', $key)
        ;
        if($tag !== null){
            $result = $result->where('tag', '=', $tag);
        }
        return $result->first();
    }
}
