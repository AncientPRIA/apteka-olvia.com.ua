<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ShopLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Models\TranslatableString;
use App\Models\Post;
use App\Models\Metadata;
use App\Helpers\Microdata;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use TCG\Voyager\Models\Translation;

class HomeController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(){
        $user = \Auth::user();
        $is_mobile = \Browser::isMobile();
        $cache_key = 'html_home';
        if($is_mobile){
            $cache_key = $cache_key.'_mobile';
        }else{
            $cache_key = $cache_key.'_desktop';
        }
        if(!$user){
            $html = Cache::get($cache_key);
            if($html !== null){
                return $html;
            }
        }

        //dd($user);
        //dd(\Auth::user(), \Auth::check(), $user->hasVerifiedEmail());

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        // Products - Featured (Season)
        $products_featured = Product::query()
            ->orderBy('created_at')
            ->limit(10)
            ->with("category")
            ->featured()
            ->get();

        // Products - Top sells
        $products_top_sells = Product::query()
            ->orderBy('selled_amount', 'desc')
            ->limit(10)
            ->with("category")
            ->get();

        $actions_ids = setting('page-home.sell-home-grid');
        $actions_ids = explode('|', $actions_ids);
        $actions = ProductCategory::query()
            ->whereIn('id', $actions_ids)
            ->get()
        ;
        /*
        $actions = Action::query()
            ->orderBy('order', 'asc')
            ->limit(20)
            ->get()
            ->toArray();
        */

        $categories_featured = ProductCategory::query()
            ->limit(6)
            ->featured()
            /*->withCount(['products' => function ($query){
                $query->published();
            }])*/
            ->orderBy('order')
            ->get();

            //dd($categories_featured);
        if(count($categories_featured) < 6){
            $categories_featured = [];
        }

        $recommended_wide_ids = [setting('page-home.recommended_wide_category_id_1'), setting('page-home.recommended_wide_category_id_2')];
        $recommended_wide = ProductCategory::query()
            ->whereIn('id', $recommended_wide_ids)
            ->get();
        ;



        $cities = City::query()
            ->with('shops')
            ->get();

        // Meta
        $meta_key = 'home';
        $metadata = get_meta($meta_key);
        if($metadata['title'] === ""){
            $metadata['title'] = "Сеть аптек Ольвия";
        }
        if($metadata['description'] === ""){
            $metadata['description'] = "Официальный сайт сети аптек Ольвия. Полный каталог лекарств и товаров для здоровья с указанием наличия в каждой аптеке в Донецке, Макеевке, Горловке, Енакиево";
        }

        // Meta END

        // Microdata
        $microdata_key = 'structured_'.$meta_key;
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata){
            $microdata = new Microdata();
            $microdata->type_website(env('APP_URL'), setting('site.title'), env('APP_URL')."search?q={search_term_string}");
            $microdata->type_organization(env('APP_URL'), setting('site.title'), asset('uploads/opengraph/logo.png'), []);
            return $microdata->generate(['website', 'organization']);
        });
        // Microdata END



        /* 
            single page styles
                true - include libs
                false - my css
        */
        $css_critical = [
            //"css/home_critical.min.css"
            "css/home.min.css"
        ];
        $styles = [
            //["css/home.min.css",false],
            //["css/libs/flickity/flickity.min.css",false], // Do not delete
        ];

        /* 
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            ["runtime~js/index.js",false],
            ["js/index.js",false],
            //["js/index_validator.js",false],
        ];

        if($user != null){
            array_push($footer_scripts, ["runtime~js/user_active.js",false],["js/user_active.js",false]);
        }

        $html = view('page/home')->with([
            'sidebar_menu' => $sidebar_menu,
            'products_featured' => $products_featured,
            'products_top_sells' => $products_top_sells,
            'user' => $user ,
            'actions' => $actions,
            'categories_featured' => $categories_featured,
            'recommended_wide' => $recommended_wide,
            'cities' => $cities,

            'css_critical' => $css_critical,
            'css' => $styles,
            'scripts' =>  $footer_scripts,
//            'critical_css' => 'home',
            'meta' => $metadata,
            'microdata' => $microdata_result,
            ])->render();
        if(!$user){
            Cache::put($cache_key, $html, 360);
        }
        return $html;
    }


    // AJAX
    // Email contact
    function email_contact(Request $request){
        $form_data = $request->only('email', 'message');

        $validator = Validator::make($form_data, [
            'email' => ['required', 'string', 'email', 'max:64'],
            'message' => ['required', 'string', 'min:6', 'max:1000'],
        ]);

        try {
            $validator->validate();

            // Do something here
            $transport = (new Swift_SmtpTransport('mail.pria.digital', 465, 'ssl'))
                ->setUsername('info@pria.digital')
                ->setPassword('q1w2e3r4')
            ;

            // Create the Mailer using your created Transport
            $mailer = new Swift_Mailer($transport);

            // Create a message
            $message = (new Swift_Message('Contact'))
                ->setFrom(['info@pria.digital' => 'Workelite'])
                ->setTo(['ancient.animated@gmail.com'])
                ->setBody("Someone with email ".$form_data['email'].", sent us a question: ".$form_data['message'])
            ;

            // Send the message
            $result = $mailer->send($message);
            if($result === 1){
                $response['status'] = '1';
                $response['content'] = ''; // Success message
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response['status'] = '0';
                $response['content'] = 'Mail server error, try later'; // Error message
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }




        }catch (ValidationException $e){
            $result = $validator->errors();

            $response['status'] = '0';
            $response['type'] = 'validation';
            $response['content'] = $result;
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

    }



    // Fills translations for countries. DO NOT DELETE PLEASE (Not yet...)
    /*
    public function countries(){

        $list_of_locales = [
            'ru',
            'en',
            'pl',
            'uk',
        ];

        $is_first = true;
        foreach ($list_of_locales as $locale){
            $json_file = storage_path('countries/'.$locale.'/world.json');
            if(File::exists($json_file)){

                $json = json_decode(File::get($json_file), true);
                foreach ($json as $row){
                    if($is_first){
                        //"id", "name", "alpha_2", "alpha_3"
                        Country::query()
                            ->insert([
                                'id'=>              $row['id'],
                                'name'=>            $row['name'],
                                'alpha_2'=>         $row['alpha2'],
                                'alpha_3'=>         $row['alpha3'],
                            ]);
                    }else{
                        // 'table_name', 'column_name', 'foreign_key', 'locale', 'value'
                        Translation::query()
                            ->insert([
                                'table_name'=>      'countries',
                                'column_name'=>     'name',
                                'foreign_key'=>     $row['id'],
                                'locale'=>          $locale,
                                'value'=>           $row['name']
                            ]);
                    }

                }

                $is_first = false;

            }else{
                echo 'No '.$locale.' file'.PHP_EOL;
            }
        }

    }
    */

    public function test(){

        /*
        $products_featured = Product::query()
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->with("category")
            ->featured()
            ->get();

        //dd($products_featured);
        dd($products_featured[0], $products_featured[0]->image);
        */

        $categories = ProductCategory::query()
            ->where('id', '=', 3)
            //->query()
            //->children()
            ->first();

        $categories->get_path();
        //dd();


        /*
        $category = ProductCategory::query()
            ->where('id', 1)
            ->des
            ->first();
        //ProductCategory::query()

        $category->get_flat_tree();
        dd($category, $category->get_flat_tree());
        */

        // Meta
        $meta_key = 'home';
        $metadata = get_meta($meta_key);
        // Meta END

        // Microdata
        /*
        $microdata_result = Cache::remember('index_structured', 3600, function () use ($meta){
            $microdata = new Microdata();
            $microdata->type_store('Rave', $meta['description'], $meta['og_image'], 'Mo-Su 11:00-22:00', '', "г.Донецк, Университетская 20");
            return $microdata->generate(['store']);
        });
        */
        // Microdata END



        /*
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
            //["css/libs/flickity/flickity.min.css",false], // Do not delete
            ["css/home.min.css",false],
//            ["css/home_buf.min.css",false]
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            ["js/index.js",false],
            ["js/index_test.js",false],
            ["js/index_validator.js",false],
        ];

        $html = view('page/home')->with([
            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'meta' => $metadata,
            //'microdata' => $microdata_result,
        ]);
        return $html;
    }


    public function test2(){
        // Meta
        $meta_key = 'crm_details_browse';
        $metadata = get_meta($meta_key);
        // Meta END

        // Microdata
        /*
        $microdata_result = Cache::remember('index_structured', 3600, function () use ($meta){
            $microdata = new Microdata();
            $microdata->type_store('Rave', $meta['description'], $meta['og_image'], 'Mo-Su 11:00-22:00', '', "г.Донецк, Университетская 20");
            return $microdata->generate(['store']);
        });
        */
        // Microdata END



        /*
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
            ["css/libs/flickity/flickity.min.css",false], // Do not delete
            ["css/home.min.css",false],
            ["css/home_buf.min.css",false]
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            ["js/index.js",false],
            ["js/index_test.js",false],
        ];

        $html = view('page/about')->with([
            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'meta' => $metadata,
            //'microdata' => $microdata_result,
        ]);
        return $html;
    }

    public function polit(){
        if(isset($_POST['polit_id'])){
            $post_id = $_POST['polit_id'];
        }else{
            $post_id = 0;
        }

        if(isset($_POST['locale'])){
            $locale = $_POST['locale'];
        }else{
            $locale = config('voyager.multilingual.default');
        }

        $post_content = Post::query()
            ->where('id', '=', $post_id)
            ->first();



        $response['status'] = '1';
        $response['content'] = $post_content["body"];
        $response['title'] = $post_content["title"];
        $response['quote'] = $post_content["excerpt"];




        return json_encode($response, JSON_UNESCAPED_UNICODE);
        //return response()->json($response)->header('Content-Type', 'application/json');
    }


    public function csrf_refresh(){

        $csrf = csrf_token();
        $response['status'] = '1';
        $response['content'] = $csrf;
        return json_encode($response,JSON_UNESCAPED_UNICODE);

    }

    public function jobs()
    {

        //$sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        // Meta
        $meta_key = 'jobs';
        $metadata = get_meta($meta_key);

        if($metadata['title'] === ""){
            $metadata['title'] = "Вакансии - Сеть аптек “Ольвия”";
        }

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);
        // Meta END

        // Microdata
        $microdata_key = 'structured_'.$meta_key;
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata){
            $microdata = new Microdata();
            $microdata->type_website(env('APP_URL'), setting('site.title'), env('APP_URL')."search?q={search_term_string}");
            $microdata->type_organization(env('APP_URL'), setting('site.title'), asset('uploads/opengraph/logo.png'), []);
            return $microdata->generate(['website', 'organization']);
        });

        $jobs_list = Post::query()
            ->whereIn('id', array(26))
            ->get();

        $metadata['og_image'] = $jobs_list[0]->image;

        $base_url = route('jobs');
        $base_breadcrumbs = [
            [
                "href"=>"/",
                "title"=>"Главная"
            ],
            [
                "href"=> $base_url,
                "title"=>"Вакансии"
            ],
        ];

        $styles = [
            ["css/contact.min.css", false],
        ];

        $footer_scripts = [
            ["runtime~js/contact.js", false],
            ["js/contact.js", false],
        ];

        $html = view('page/jobs')->with([
            'css' => $styles,
            'scripts' => $footer_scripts,
            'meta' => $metadata,
            'breadcrumbs'=>$base_breadcrumbs,
            'sidebar_menu' => $sidebar_menu,
            'jobs'=>$jobs_list,
            //'microdata' => $microdata_result,
        ]);
        return $html;
    }

    public function partners()
    {

        //$sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        // Meta
        $meta_key = 'contacts';
        $metadata = get_meta($meta_key);
        // Meta END

        // Microdata
        $microdata_key = 'structured_'.$meta_key;
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata){
            $microdata = new Microdata();
            $microdata->type_website(env('APP_URL'), setting('site.title'), env('APP_URL')."search?q={search_term_string}");
            $microdata->type_organization(env('APP_URL'), setting('site.title'), asset('uploads/opengraph/logo.png'), []);
            return $microdata->generate(['website', 'organization']);
        });

        $list = Post::query()
            ->whereIn('id', array(27))
            ->get();

        $base_url = route('partners');
        $base_breadcrumbs = [
            [
                "href"=>"/",
                "title"=>"Главная"
            ],
            [
                "href"=> $base_url,
                "title"=>"Вакансии"
            ],
        ];

        $styles = [
            ["css/contact.min.css", false],
        ];

        $footer_scripts = [
            ["runtime~js/contact.js", false],
            ["js/contact.js", false],
        ];

        $html = view('page/partners')->with([
            'css' => $styles,
            'scripts' => $footer_scripts,
            'meta' => $metadata,
            'breadcrumbs'=>$base_breadcrumbs,
            'partners'=>$list,
            //'microdata' => $microdata_result,
        ])->render();

        return $html;
    }
}
