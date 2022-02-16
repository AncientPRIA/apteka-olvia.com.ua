<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\App;
use TCG\Voyager\Traits\Translatable;


class TranslatableString extends Model
{
    use Translatable;

    protected $translatable = ['value'];
    protected $guarded = [];


    public function get_all_key_value(){

        /*
        $strings = TranslatableString::query()
            ->select('key', 'value')
            ->with('translations')
            ->get();
            //->pluck('value', 'key')
            //->toArray();
        */

        $strings = TranslatableString::get()
            ->translate(App::getLocale(), config('app.locale_front'))
            ->pluck('value', 'key')
            ->toArray();
        //$strings->translate(App::getLocale(), config('app.locale_front'));
        return $strings;
    }
}
