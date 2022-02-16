<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ProductRating extends Model
{
    public $timestamps = false;






    public function save(array $options = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->user_id && Auth::user()) {
            $this->user_id = Auth::user()->getKey();
        }

        parent::save();
    }

    public function authorId()
    {
        return $this->belongsTo(Voyager::modelClass('User'), 'user_id', 'id');
    }

}
