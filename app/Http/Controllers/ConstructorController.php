<?php

namespace App\Http\Controllers;

// Models
use App\Models\Product;
use App\User;
use App\Models\Metadata;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Translation;
use App\Models\Category;
use App\Models\Material;

// Tools
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

use Illuminate\Support\Carbon;

use Illuminate\Support\Str;
use Cocur\Slugify\Slugify;






class ConstructorController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(){

        // Language
        $locales = config('voyager.multilingual.locales');
        $locale = config('app.locale_current');
        // Language END

        $user = \Auth::user();
        $user = User::query()->first();
        if($user === null){
            //Redirect::route('login_'.$locale);
            //redirect('login');
            return redirect()->route('login_'.$locale);
        }
        //dd($user);

        // Meta
        $meta_key = 'crm_details_edit';
        $metadata = get_meta($meta_key);
        // Meta END

        /*
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
            //["css/libs/flickity/flickity.min.css",false],
            ["css/constructor.css",false]
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            //["js/libs/flickity/flickity.pkgd.min.js",false],
            //["js/libs/wow/wow.min.js",false],
            ["js/constructor.js",false],
        ];

        $title_tollbar = "Обработка";

        // widgets
        $widgets=array();

        $widgets['widget-1'] =[
            'title' => "Обработка торцов",
            'class_modif_widget' => "widget-1",
            'class_modif_list'=> "torec",
            'include_content' => 0
        ];
        // widgets 2
        $widgets['widget-2']=[
            'title' => "Сверление",
            'class_modif_widget' => "widget-2",
            'class_modif_list'=> "sverlo",
            'include_content' => 1
        ];

        //widget 3
        $widgets['widget-3']=[
            'title' => "Вырез",
            'class_modif_widget' => "widget-3",
            'class_modif_list'=> "virez",
            'include_content' => 2
        ];

        // widgets END
        $html = view('page/constructor')->with([
            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'title_tollbar'=>$title_tollbar,
            'locale' => $locale,
            'locales' => $locales,
            'meta' => $metadata,
            'widgets' => $widgets,
            'user' => $user,
        ]);
        return $html;
    }

    public function create(){

        // Language
        $locales = config('voyager.multilingual.locales');
        $locale = config('app.locale_current');
        // Language END


        $user = \Auth::user();
        if($user === null){
            //Redirect::route('login_'.$locale);
            //redirect('login');
            //return redirect()->route('login_'.$locale);
        }

        //dd($user);

        $materials = [];
        $materials['glass'] = Category::query()
            ->where('id', 15)
            ->with('materials')
            ->first()
            ->translate(App::getLocale(), config('app.locale_front'));
        $materials['mirror'] = Category::query()
            ->where('id', 16)
            ->with('materials')
            ->first()
            ->translate(App::getLocale(), config('app.locale_front'));


        // Meta
        $meta_key = 'crm_details_create';
        $metadata = get_meta($meta_key);
        // Meta END

        /*
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
            //["css/libs/flickity/flickity.min.css",false],
            ["css/material.css",false]
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            //["js/libs/flickity/flickity.pkgd.min.js",false],
            //["js/libs/wow/wow.min.js",false],
            ["js/libs/inputmask/jquery.inputmask.bundle.js",false],
            ["js/material.js",false],
        ];

        $title_tollbar = "Обработка";



        $html = view('page/create')->with([
            'materials' => $materials,

            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'title_tollbar'=>$title_tollbar,
            'locale' => $locale,
            'locales' => $locales,
            'meta' => $metadata,
            'user' => $user,
            'datetime' => date('y-m-d_H-i', strtotime(config('app.timezone'))),

            'name_edit_disable' => true,

        ]);
        return $html;
    }


    /* AJAX */

    public function product_create(Request $request){
        $locale = $request->get('locale');
        if($locale === null){
            $locale = config('app.locale_front');
        }

        $form_data = $request->all();
        $material_id  = $request->get('material_id');

        if($material_id !== null){
            $material = Material::query()
                ->where('id', $material_id)
                ->first();
        }
        if($material === null){
            $response['status'] = '0';
            //$response['type'] = 'validation';
            $response['content'] = 'Material not exists';
        }

        $validator = Validator::make($form_data, [
            'name'          => ['required', 'string', 'max:255'],
            //'material_id'   => ['required', 'integer'],
            'width'   => ['required', 'integer', 'min:'.$material->min_width, 'max:'.$material->max_width],
            'length'   => ['required', 'integer', 'min:'.$material->min_length, 'max:'.$material->max_length],
            'thickness'   => ['required', 'integer', 'min:'.$material->min_thickness, 'max:'.$material->max_thickness],
            'count'   => ['required', 'integer', 'min:1'],
        ]);

        try {
            $validator->validate();

            $user = Auth::user();

            $slugify = new Slugify();
            $slug = $slugify->slugify($form_data['name']);

            //var_dump($form_data, $material_id);
            $new_detail = new Product();
            $new_detail->title = /*"CRK";*/ $form_data['name'];
            $new_detail->slug = $slug; //Str::slug($form_data['name'], '-');
            $new_detail->status = 'PUBLISHED';
            $new_detail->author_id = $user->id;
            $new_detail->material_id = $material_id;
            $new_detail->width = $form_data['width'];
            $new_detail->length = $form_data['length'];
            $new_detail->thickness = $form_data['thickness'];
            $new_detail->count = $form_data['count'];

            //dd($new_detail);

            $new_detail->save();

            if($new_detail->id !== null){
                $response['status'] = '1';
                $response['redirect'] = route('home_'.$locale);
                $response['content'] = '';
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response['status'] = '0';
                $response['type'] = 'db';
                $response['content'] = 'Error! Cant add new product';
            }


        }catch (ValidationException $e){
            $result = $validator->errors();

            $response['status'] = '0';
            $response['type'] = 'validation';
            $response['content'] = $result;
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }


    }

    /* AJAX END */
}
