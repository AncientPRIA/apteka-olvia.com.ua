<?php

namespace App\Http\Controllers;

use App\Helpers\Microdata;
use App\Models\Country;
use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductRating;
use App\Models\Review;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

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

class ProductsController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    // Decide Archive or Index
    public function index($path){

        /* Dummy
        $segments = [
            0 => "kategoriya-1",
            1 => "kategoriya-2",
            2 => "kategoriya-3",
        ];
        */

        $segments = explode('/', $path);
        $category = ProductCategory::find_by_path($segments)->first();
        //dd($tmp->toSql(), $tmp->getBindings(), $tmp->get());

        if($category !== null){
            // Category found -> Success 200
            //dd('Category found -> Success 200');
            return $this->archive($category, $path);
        }else{
            // Category not found, maybe single?
            $segments_count = count($segments);
            $product_slug = array_pop($segments);
            if($segments_count > 1){
                // More than one slug means there is must be category for single
                $category = ProductCategory::find_by_path($segments)->first();
                if($category === null){
                    // No category for single slug -> Fail 404
                    //dd('No category for single slug -> Fail 404');
                    abort(404);

                }else{
                    $product = Product::query()
                        ->where('category_id', $category->id)
                        ->where('slug', $product_slug)
                        ->published()
                        ->first();
                    if($product === null){
                        // Category exists, but not single -> Fail 404
                        //dd('Category exists, but not single -> Fail 404');

                        abort(404);
                    }else{
                        // Category and single exist -> Success 200
                        //dd('Category and single exist -> Success 200');
                        return $this->single($product, $category);
                    }
                }
            }else{
                // One slug means single doesn't have category
                $product = Product::query()
                    ->where('category_id', null)
                    ->where('slug', $product_slug)
                    ->published()
                    ->first();
                if($product === null){
                    // single not exists -> Fail 404
                    //dd('single not exists -> Fail 404');
                    abort(404);
                }else{
                    // single found -> Success 200
                    //dd('single found -> Success 200');
                    return $this->single($product, $category);
                }
            }
        }
    }

    // Archive
    public function archive($category = null, $path = null){
        if($category === null){
            // Root
            $category_id = null;
            $category_name = "Медикаменты и товары";
            $meta_category_title = "Купить медикаменты и товары";
            $meta_category_description = null;
            // Meta
            $meta_key = 'products';
            $metadata = get_meta($meta_key);

            if($metadata['title'] === ""){
                $metadata['title'] = "Купить медикаменты в Донецке, Горловке, Макеевке, Енакиево - Сеть аптек “Ольвия”";
            }

            if($metadata['description'] === ""){
                $metadata['description'] = "Купить медикаменты в Донецке, Горловке, Макеевке, Енакиево ➨Переходи на сайт!";
            }
            // Meta END

            $strings = get_strings();
            $category_description = string($strings, "product_category_is_null_description", "");

            $image_header = null;
        }else{
            // Some category
            $category_id = $category->id;
            // Meta
            $metadata = get_meta(null, $category);
            // Meta END

            if($category->meta_h1 !== null AND $category->meta_h1 !== ""){
                $category_name = $category->meta_h1;
            }else{
                $category_name = $category->name;
            }

            if($category->meta_title !== null AND $category->meta_title !== ""){
                $meta_category_title = $category->meta_title;
            }else{
                $meta_category_title = "".$category->name." в Донецке, Горловке, Макеевке, Енакиево - Сеть аптек “Ольвия”";
            }

            if($category->meta_description !== null AND $category->meta_description !== ""){
                $meta_category_description = $category->meta_description;
            }else{
                $meta_category_description = "".$category->name." в Донецке, Горловке, Макеевке, Енакиево ➨Переходи на сайт!";
            }


            $category_description = $category->body ?? null;

            $root = $category->get_root();
            $image_header = $root->image_header ?? null;
        }



        if(isset($_COOKIE['products_sort'])){
            $sorting = $_COOKIE['products_sort'];
        }else{
            $sorting = 'created_at|desc';
        }

        $sorting_options = config('products.sorting_options');

        $request = request();
        $page = $request->query('page', 1);
        $cache_category_id = $category_id ?? 'null';

        $products = Cache::remember('cat_'.$cache_category_id.'_sort_'.$sorting.'_page_'.$page, 2880, function () use ($category_id, $sorting){
            $products = Product::macro_get_products($category_id, null, $sorting);
            return $products;
        });

        $sidebar_menu = ProductCategory::menu_tree_simple(null, $category_id);

        //head_pagination
        $head_pagination = [];
        $prev_page = $products->previousPageUrl();
        $next_page = $products->nextPageUrl();

        if($prev_page !== null){
            $head_pagination += ['prev' => $prev_page];
        }

        if($next_page !== null){
            $head_pagination += ['next' => $next_page];
        }
        //END head_pagination

        // Breadcrumbs
        $base_url = route('products');
        $base_breadcrumbs = [
                [
                    "href"=>"/",
                    "title"=>"Главная"
                ],
                [
                    "href"=> $base_url,
                    "title"=>"Все товары"
                ],
            ];
        if($category !== null){
            $breadcrumbs = $category->get_breadcrumbs($base_url, $base_breadcrumbs);
        }else{
            $breadcrumbs = $base_breadcrumbs;
        }

        // Breadcrumbs END


        // Microdata
        $microdata_key = 'structured_products';
        if($category_id !== null){
            $microdata_key .= $category_id;
        }
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata, $products){
            $phone = explode('|', setting('site.phones'));
            if(isset($phone[0])){
                $phone = trim($phone[0]);
            }
            $microdata = new Microdata();
            $microdata->type_store(setting('site.title'), $metadata['description'], $metadata['og_image'], 'Mo-Su 11:00-22:00', $phone, setting('site.address'));
            foreach ($products as $product){
                $microdata->type_product($product->category->name ?? '', $product->title, $product->body, $product->image, route('products').'/'.$product->get_path(), $product->price, 'RUB');
            }
            return $microdata->generate(['store', 'product']);
        });
        // Microdata END

        if($page > 1){
            $page_text = " - Страница ".$page;
        }else{
            $page_text = "";
        }


        // Meta
        if($metadata['title'] === ""){
            $metadata['title'] = $meta_category_title.$page_text;
        }
        if($metadata['description'] === ""){
            $metadata['description'] = $meta_category_description;
        }

        $metadata['og_image'] = $image_header;
        // Meta END

        /*
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
            ["css/products.min.css",false],
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            ["runtime~js/products.js",false],
            ["js/products.js",false],
        ];

        $user = \Auth::user();
        if($user != null){
            array_push($footer_scripts, ["runtime~js/user_active.js",false],["js/user_active.js",false]);
        }

        $html = view('page/products')->with([
            'sorting_options' => $sorting_options,
            'sorting' => $sorting,
            'products' => $products,
            'category_id' => $category_id,
            'category_name' => $category_name,
            'category_description' => $category_description,
            'sidebar_menu' => $sidebar_menu,
            'breadcrumbs' => $breadcrumbs,
            'image_header' => $image_header,

            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'meta' => $metadata,
            'microdata' => $microdata_result,
            'head_pagination' => $head_pagination
        ]);
        return $html;
    }

    // Single product
    public function single($product = null, $category = null){
//        dd('We are in single func');
        $user = user_verified();

        if($product === null){
            abort(404);
        }else{
            $current_product = $product;
        }

        if($user !== false){
            $user_rating = $current_product->get_user_rating($user->id);
            if($user_rating !== null){
                $user_rating = $user_rating->rating;
            }else{
                $user_rating = 0;
            }
        }else{
            $user_rating = 0;
        }

        $rating_average = (int)$current_product->get_average_rating();

        if($category === null){
            $current_category = null;
            //abort(404);
        }else{
            $current_category = $category;
        }

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        //parameters
        $allowed_to = [
            "Взрослым" => $current_product->allowed_adult,
            "Детям" => $current_product->allowed_child,
            "Беременным" => $current_product->allowed_pregnant,
            "Кормящим" => $current_product->allowed_nursing,
            "Аллергикам" => $current_product->allowed_allergic,
            "Диабетикам" => $current_product->allowed_diabetic,
            "Водителям" => $current_product->allowed_driver,
        ];
        $allowed_to_not_null = false;
        foreach ($allowed_to as $value){
            if($value !== null){
                $allowed_to_not_null = true;
                break;
            }
        }
        if(!$allowed_to_not_null){
            $allowed_to = null;
        }

        $parameters = [
            "Торговое название" => $current_product->title_short,
            "Действующие вещества" => $current_product->active_substances() ? join(', ', $current_product->active_substances()->pluck('title')->toArray()) : null,
            "Кому можно" => $allowed_to,
            "Форма выпуска" => $current_product->release_form,
            "Способ введения" => $current_product->administration,
            "Вид упаковки" => $current_product->packaging_type,
            "Количество в упаковке" => $current_product->count_in_package,
            "Фасовка" => $current_product->packing,
            "Взаимодействие с едой" => $current_product->food_interaction,
            "Чувствительность к свету" => $current_product->light_sensitivity,
            "Условия отпуска" => $current_product->conditions_of_supply,
            "Срок годности" => $current_product->expiration_date,
            "Температура хранения" => $current_product->storage_temperature,
            "Производитель" => $current_product->manufacturer,
            "Страна владелец лицензии" => $current_product->country_license_holder,
            "Код АТС" => $current_product->atc_code,
        ];
        //parameters END

        //img_array
        $img_array = json_decode($current_product["image"], true) ?? [$current_product->no_image];

        $reviews = Review::where('product_id', $current_product['id'])
            ->orderBy('created_at', 'desc')
            ->published()
            ->paginate(null, $columns = ['*'], $pageName = 'page', 0);

        //cities
        $cities = City::query()
            ->with('shops')
            ->get();

        $active_substenaces_ids = $current_product->active_substances()->pluck('id');
        if(count($active_substenaces_ids) > 0){
            $analogs = Product::query()
                ->published()
                ->whereHas('active_substances', function ($query) use ($active_substenaces_ids){
                    $query->whereIn('active_substance_id', $active_substenaces_ids);
                })
                ->limit(10)
                ->get();
        }else{
            $analogs = [];
        }

        $related_products = $current_product->related_by_order();


        // Breadcrumbs
        $base_url = route('products');
        $base_breadcrumbs = [
            [
                "href"=>"/",
                "title"=>"Главная"
            ],
            [
                "href"=> $base_url,
                "title"=>"Все товары"
            ],
        ];
        if($current_category !== null){
            $breadcrumbs = $current_category->get_breadcrumbs($base_url, $base_breadcrumbs);
        }else{
            $breadcrumbs = $base_breadcrumbs;
        }
        $breadcrumbs[] = [
            'title' => $current_product['title'],
        ];

        // Breadcrumbs END

        // Meta
        $meta_key = 'single_product'.$current_product['id'];
        $metadata = get_meta(null, $current_product);

        if($current_product->meta_title !== null AND $current_product->meta_title !== ""){
            $meta_current_product_title = $current_product->meta_title;
        }else{
            $meta_current_product_title = "Купить ".$current_product->title." - Сеть аптек “Ольвия”";
        }

        if($current_product->meta_description !== null AND $current_product->meta_description !== ""){
            $meta_current_product_description = $current_product->meta_description;
        }else{
            $meta_current_product_description = "Купить ".$current_product->title." Инструкция к препарату, состав, показания к применению";
        }

        if($metadata['title'] === ""){
            $metadata['title'] = $meta_current_product_title;
        }
        if($metadata['description'] === ""){
            $metadata['description'] = $meta_current_product_description;
        }
        // Meta END

        // Microdata
        $microdata_key = 'structured_'.$meta_key;
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata, $current_product){
            $microdata = new Microdata();
            $microdata->type_product($current_product->category->name ?? '', $current_product->title, $current_product->body, $current_product->image, route('products').'/'.$current_product->get_path(), $current_product->price, 'RUB');
            return $microdata->generate(['product']);
        });
        // Microdata END

        $styles = [
            ["css/libs/flickity/flickity.min.css",false], // Do not delete
            ["css/single_product.css",false],
            ["css/photoswipe_skin.css",false],
            ["css/single_product.min.css",false],

        ];

        $footer_scripts=[
            //["runtime~js/products.js",false],
            ["runtime~js/single_product.js",false],
            ["js/single_product.js",false],
            ["js/index_validator.js",false],
        ];

        $user = \Auth::user();
        if($user != null){
            array_push($footer_scripts, ["runtime~js/user_active.js",false],["js/user_active.js",false]);
        }

        $html = view('page/single_product')->with([
//            'products' => $products,
//            'category_id' => $category_id,
            'breadcrumbs' => $breadcrumbs,
            'sidebar_menu' => $sidebar_menu,
            'cities' => $cities,
            'img_array' => $img_array,
            'parameters' => $parameters,
            'current_category' => $current_category,
            'current_product' => $current_product,
            'reviews' => $reviews,
            'analogs' => $analogs,
            'related_products' => $related_products,
            'user_rating' => $user_rating,
            'rating_average' => $rating_average,
            'user' => $user,

            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'meta' => $metadata,
            //'microdata' => $microdata_result,
        ]);
        return $html;
    }

    //discount page
    public function discount_product_index(){

//        if($category === null){
//            // GET
//            $category_id = null;
//        }else{
//            $category_id = $category->id;
//        }

//        $products = Product::macro_get_products($category_id);

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        $products = Product::query()
            ->whereNotNull('discount', 'and')
            ->where('discount', '!=', 0)
            ->get();

        // Meta
        $meta_key = 'discounted_products';
        $metadata = get_meta($meta_key);
        // Meta END

        // Microdata

        $microdata_key = 'structured_discounted_products';
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata, $products){
            $phone = explode('|', setting('site.phones'));
            if(isset($phone[0])){
                $phone = trim($phone[0]);
            }
            $microdata = new Microdata();
            $microdata->type_store('Rave', $metadata['description'], $metadata['og_image'], 'Mo-Su 11:00-22:00', $phone, setting('site.address'));
            foreach ($products as $product){
                $microdata->type_product($product->category->name ?? '', $product->title, $product->body, $product->image, route('products').'/'.$product->get_path(), $product->price, 'RUB');
            }
            return $microdata->generate(['store', 'product']);
        });

        $styles = [
            ["css/discount_products.min.css",false],
        ];

        $footer_scripts=[
            //["runtime~js/products.js",false],
            ["runtime~js/discount_products.js",false],
            ["js/discount_products.js",false],
        ];

        $html = view('page/discount_products')->with([
            'sidebar_menu' => $sidebar_menu,
            'products' => $products,
//            'category_id' => $category_id,

            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'meta' => $metadata,
            'microdata' => $microdata_result,
        ]);
        return $html;
    }



    public function load_more(Request $request){
        $params = $request->only(['page', 'category_id', 'sorting']);

        if($params['page'] === null){
            $response['status'] = '0';
            $response['content'] = 'Error: page is null';
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

        $products = Product::macro_get_products($params['category_id'], $params['page']+1, $params['sorting']);
        $html = '';
        foreach ($products as $product){
            $html .= view('blocks.slider.items.slider_product_item')->with([
                'item' => $product,
            ]);
        }


        $response['status'] = '1';
        $response['content'] = $html;
        $response['count'] = count($products);
        return json_encode($response,JSON_UNESCAPED_UNICODE);
    }

    public function product_rate(Request $request){
        $params = $request->only(['rating', 'user_id', 'product_id']);
        $user = user_verified();
        if($user['id'] !== (int)$params['user_id']){
            $response['status'] = '0';
            $response['type'] = 'wrong_user';
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

        $rate = ProductRating::where('user_id', '=', $user->id)
            ->where('product_id', '=', $params['product_id'])
            ->first();
        if($rate !== null){
            $rate->rating = $params['rating'];
            $rate->save();
        }else{
            $rate = new ProductRating();
            $rate->user_id = $user->id;
            $rate->product_id = $params['product_id'];
            $rate->rating = $params['rating'];
            $rate->save();
        }

        $response['status'] = '1';
        return json_encode($response,JSON_UNESCAPED_UNICODE);

    }




}
