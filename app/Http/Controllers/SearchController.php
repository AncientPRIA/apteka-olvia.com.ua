<?php

namespace App\Http\Controllers;

use App\Helpers\Microdata;
use App\Models\Country;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

use App\Models\Product;

use App\Models\TranslatableString;
use App\Models\Post;
use App\Models\Metadata;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use TCG\Voyager\Models\Translation;

class SearchController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(){

        $user = \Auth::user();
        // Language
        $locales = config('voyager.multilingual.locales');
        $locale = config('app.locale_current');
        // Language END

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);


        $keyword = trim(htmlspecialchars(Input::get('q'), ENT_QUOTES));
        $keyword = mb_ereg_replace('/([^а-яa-z 1-9+-])/i', '', $keyword);

        // Meta
        //$meta_key = 'search';
        $metadata = get_meta(null);
        // Meta END

        $styles = [
            ["css/search.min.css",false]
        ];

        $footer_scripts=[
            ["runtime~js/search.js",false],
            ["js/search.js",false],
            ["runtime~js/user_active.js",false],
            ["js/user_active.js",false]
        ];

        if($user != null){
            array_push($footer_scripts, ["runtime~js/user_active.js",false],["js/user_active.js",false]);
        }

        if($keyword !== null && $keyword !==""){
            $search = Product::query()
                ->where('title', 'rlike', preg_quote($keyword))
                //->orWhere('body', 'like', $keyword.'%')
                ->take(16)
                ->get();
        } else {
            $search = [];
        }


        $html = view('page/search')->with([
            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'search' => $search,
            'keyword'=> $keyword,
            'sidebar_menu' => $sidebar_menu,
            'locale' => $locale,
            'locales' => $locales,
            'meta' => $metadata,
            'user' => $user,
        ]);
        return $html;
    }

    public function smart_search(Request $request){
        $search = $request->get('search');
        $search = mb_ereg_replace('/([^а-яa-z 1-9+-])/i', '', $search);
        $found = Product::query()
            ->where('title', 'rlike', preg_quote($search))
            //->orWhere('body', 'like', '%'.$search.'%')
            ->take(20)
            ->published()
            ->get('title')
            ->toArray()
        ;
        $response['status'] = '1';
        $response['content'] = $found;
        return json_encode($response,JSON_UNESCAPED_UNICODE);
    }

}
