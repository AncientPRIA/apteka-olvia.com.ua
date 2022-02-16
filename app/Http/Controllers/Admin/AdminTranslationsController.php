<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Facades\Voyager;

class AdminTranslationsController extends Controller{

    public function index()
    {

        $current_locale = App::getLocale();
        $locales = config('voyager.multilingual.locales');
        dd($current_locale);

        $translations = array();
        foreach ($locales as $locale){
            $translation_file_frontend = base_path().'/resources/lang/'.$locale.'/frontend.php';
            if(file_exists($translation_file_frontend)){

            }else{

            }

        }



        // GET THE DataType based on the slug
        //$dataType = DB::table('menu_items')->where('route', '=', 'voyager.translations.index')->first();

        //dd($dataType);


        return view('vendor/voyager/translations/index')->with([
            'isModelTranslatable' => true
        ]);
    }

}