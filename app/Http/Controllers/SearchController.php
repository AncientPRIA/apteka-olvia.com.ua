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

    public function multi_search(){
        $request = request();

        $search = $request->get("query", "");

        if(mb_strlen($search) < 3){
            $response['status'] = "0";
            $response['content'] = "";
            return;
        }

        $search = mb_strtolower($search, 'UTF-8');
        $search_arr = explode(" ", $search);
        $search_query = [];
        foreach ($search_arr as $word){
            $len = mb_strlen($word, 'UTF-8');

            switch (true)
            {
                case ($len <= 3):
                    {
                        $search_query[] = $word . "*";
                        break;
                    }
                case ($len > 3 && $len <= 6):
                    {
                        $search_query[] = mb_substr($word, 0, -1, 'UTF-8') . "*";
                        break;
                    }
                case ($len > 6 && $len <= 9):
                    {
                        $search_query[] = mb_substr($word, 0, -2, 'UTF-8') . "*";
                        break;
                    }
                case ($len > 9):
                    {
                        $search_query[] = mb_substr($word, 0, -3, 'UTF-8') . "*";
                        break;
                    }
                default:
                    {
                        break;
                    }
            }
        }

        $search_query = array_unique($search_query, SORT_STRING);
        $search_normalized = implode(" ", $search_query);


        $results = Product::query()
            ->whereRaw(
            "MATCH( title ) AGAINST(? IN BOOLEAN MODE)",  // MATCH( x, y ) - allows multiple fields
            $search_normalized
        )->get(["id", "title", "price", "slug"]);

        for($i = 0; $i < $results->count(); $i++){
            $results[$i]["url"] = route('products') . "/" . $results[$i]->get_path();
            $images = $results[$i]->get_images("thumb");
//            if($results[$i]->id === 8957){
//                dd($images);
//            }
            $results[$i]["picture"] = $results[$i]->picture($images[0], "search");
        }


        $response['status'] = "1";
        $response['content'] = $results;
        return $response;
    }

    public function test_multi_search(){
        $info = [];
        $et = microtime(true);

        $request = request();

        $search = $request->get("query", "");

        $info["search"] = $search;

        if(mb_strlen($search) < 3){
            return;
        }

        $search = mb_strtolower($search, 'UTF-8');
        $search_arr = explode(" ", $search);
        $search_query = [];
        foreach ($search_arr as $word){
            $len = mb_strlen($word, 'UTF-8');

            switch (true)
            {
                case ($len <= 3):
                    {
                        $search_query[] = $word . "*";
                        break;
                    }
                case ($len > 3 && $len <= 6):
                    {
                        $search_query[] = mb_substr($word, 0, -1, 'UTF-8') . "*";
                        break;
                    }
                case ($len > 6 && $len <= 9):
                    {
                        $search_query[] = mb_substr($word, 0, -2, 'UTF-8') . "*";
                        break;
                    }
                case ($len > 9):
                    {
                        $search_query[] = mb_substr($word, 0, -3, 'UTF-8') . "*";
                        break;
                    }
                default:
                    {
                        break;
                    }
            }
        }

        $search_query = array_unique($search_query, SORT_STRING);
        $search_normalized = implode(" ", $search_query);


        $results = Product::query()
            ->whereRaw(
                "MATCH( title ) AGAINST(? IN BOOLEAN MODE)",  // MATCH( x, y ) - allows multiple fields
                $search_normalized
            )->paginate();

        $info["found_count"] = $results->count();
        $info["query_time"] = microtime(true) - $et;

        dd($info, $results);
    }
}
