<?php

namespace App\Http\Controllers;

use App\Helpers\Microdata;
use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductCategory;
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

class BlogController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index($path){
        $segments = explode('/', $path);
        $category = Category::find_by_path($segments)->first();
        //dd($tmp->toSql(), $tmp->getBindings(), $tmp->get());

        if($category !== null){
            // Category found -> Success 200
            //dd('Category found -> Success 200');
            return $this->archive($category, $path);
        }else{
            // Category not found, maybe single?
            $segments_count = count($segments);
            $single_slug = array_pop($segments);
            if($segments_count > 1){
                // More than one slug means there is must be category for single
                $category = Category::find_by_path($segments)->first();
                if($category === null){
                    // No category for single slug -> Fail 404
                    //dd('No category for single slug -> Fail 404');
                    abort(404);
                }else{
                    $single = Post::query()
                        ->where('category_id', $category->id)
                        ->where('slug', $single_slug)
                        ->published()
                        ->first();
                    if($single === null){
                        // Category exists, but not single -> Fail 404
                        //dd('Category exists, but not single -> Fail 404');
                        abort(404);
                    }else{
                        // Category and single exist -> Success 200
                        //dd('Category and single exist -> Success 200');
                        return $this->single($single, $category);
                    }
                }
            }else{
                // One slug means single doesn't have category
                $single = Post::query()
                    ->where('category_id', null)
                    ->where('slug', $single_slug)
                    ->published()
                    ->first();
                if($single === null){
                    // single not exists -> Fail 404
                    //dd('single not exists -> Fail 404');
                    abort(404);
                }else{
                    // single found -> Success 200
                    //dd('single found -> Success 200');
                    return $this->single($single, $category);
                }
            }
        }
    }

    // Archive
    public function archive($category = null, $path = null){
        //Category::node_trait_fill_table();
        if($category === null){
            // Root
            $category_id = null;
            // Meta
            $meta_key = 'blog';
            $metadata = get_meta($meta_key);

            if($metadata['title'] === ""){
                $metadata['title'] = "Блог - Сеть аптек “Ольвия”";
            }

            if($metadata['description'] === ""){
                $metadata['description'] = "Блог - Сеть аптек “Ольвия”";
            }

            // Meta END
        }else{
            // Some category
            $category_id = $category->id;
            // Meta
            $meta_key = 'blog_'.$category->id;
            $metadata = get_meta(null, $category);

            if($metadata['title'] === ""){
                $metadata['title'] = $category->name." - Сеть аптек “Ольвия”";
            }

            if($metadata['description'] === ""){
                $metadata['description'] = $category->name." - Сеть аптек “Ольвия”";
            }
            // Meta END
        }

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        // posts
        $posts = Post::macro_get_posts($category_id);

        //head_pagination
        $head_pagination = [];
        $prev_page = $posts->previousPageUrl();
        $next_page = $posts->nextPageUrl();

        if($prev_page !== null){
            $head_pagination += ['prev' => $prev_page];
        }

        if($next_page !== null){
            $head_pagination += ['next' => $next_page];
        }
        //END head_pagination

        //category
        $categories = Category::query()
            ->with('posts')
            ->get();

        $posts_sidebar_1 = Post::query()
            ->orderBy('created_at', 'desc')
            ->featured()
            ->published()
            ->limit(5)
            ->get()
        ;

        // Breadcrumbs
        $base_url = route('blog');
        $base_breadcrumbs = [
            [
                "href"=>"/",
                "title"=>"Главная"
            ],
            [
                "href"=> $base_url,
                "title"=>"Блог"
            ],
        ];
        if($category !== null) {
            $breadcrumbs = $category->get_breadcrumbs($base_url, $base_breadcrumbs);
        }else{
            $breadcrumbs = $base_breadcrumbs;
        }
        // Breadcrumbs END

        // Microdata
        $microdata_key = 'structured_'.$meta_key;
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata){
            $microdata = new Microdata();
            $microdata->type_website(env('APP_URL'), setting('site.title'), env('APP_URL')."search?q={search_term_string}");
            return $microdata->generate(['website']);
        });

        // Microdata END



        /*
     single page styles
         true - include libs
         false - my css
 */
        $styles = [
            ["css/libs/flickity/flickity.min.css",false], // Do not delete
            ["css/blog.min.css",false],
//            ["css/home_buf.min.css",false]
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            ["runtime~js/blog.js",false],
            ["js/blog.js",false],
            ["js/index_validator.js",false],
        ];

        $html = view('page/blog')->with([
            'breadcrumbs' => $breadcrumbs,
            'sidebar_menu' => $sidebar_menu,
            'category_id' => $category_id,
            'posts' => $posts,
            'categories' => $categories,
            'posts_sidebar_1' => $posts_sidebar_1,
            'category' => $category,
            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'meta' => $metadata,
            'microdata' => $microdata_result,
            'head_pagination' => $head_pagination
        ]);
        return $html;
    }

    // Single product
    public function single($single = null, $category = null){
        if($single === null){
            abort(404);
        }
        if($category === null){
            abort(404);
        }

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        $posts_sidebar_1 = Post::query()
            ->orderBy('created_at', 'desc')
            ->featured()
            ->published()
            ->limit(5)
            ->get()
        ;

        // Breadcrumbs
        $base_url = route('blog');
        $base_breadcrumbs = [
            [
                "href"=>"/",
                "title"=>"Главная"
            ],
            [
                "href"=> $base_url,
                "title"=>"Блог"
            ],
        ];
        $breadcrumbs = $category->get_breadcrumbs($base_url, $base_breadcrumbs);
        $breadcrumbs[] = [
            'title' => $single['title'],
        ];
        // Breadcrumbs END

        // Meta
        $meta_key = 'single_post'.$single['id'];
        $metadata = get_meta(null, $single);

        if($single->meta_title !== null AND $single->meta_title !== ""){
            $meta_current_post_title = $single->meta_title;
        }else{
            $meta_current_post_title = $single->title;
        }

        if($metadata['title'] === ""){
            $metadata['title'] = $meta_current_post_title;
        }
//        if($metadata['og_image'] === ""){
            $metadata['og_image'] = $single->image;
//        }
        // Meta END

        // Microdata
        $microdata_key = 'structured_'.$meta_key;
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata, $single){
            $url = route('blog').'/'.$single->get_path();
            $logo = asset('uploads/opengraph/logo.png');
            $microdata = new Microdata();
            $microdata->type_website(env('APP_URL'), setting('site.title'), env('APP_URL')."search?q={search_term_string}");
            $microdata->type_article($single['title'], $url, $single->created_at->format('Y-m-d'), $single->updated_at->format('Y-m-d'), $single['body'], $single->category->name, $single->image, setting('site.title'), $logo, setting('site.title'));
            return $microdata->generate(['website', 'article']);
        });
        // Microdata END



        /*
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
//            ["css/libs/flickity/flickity.min.css",false], // Do not delete
            ["css/blog.min.css",false],
//            ["css/home_buf.min.css",false]
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            ["runtime~js/blog.js",false],
            ["js/blog.js",false],
//            ["js/index_test.js",false],
        ];

        $html = view('page/single_post')->with([
            'breadcrumbs' => $breadcrumbs,
            'sidebar_menu' => $sidebar_menu,
            'post' => $single,
            'posts_sidebar_1' => $posts_sidebar_1,

            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'meta' => $metadata,
            'microdata' => $microdata_result,
        ]);
        return $html;
    }

    public function load_more(Request $request){
        $params = $request->only(['page', 'category_id']);

        if($params['page'] === null){
            $response['status'] = '0';
            $response['content'] = 'Error: page is null';
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

        $items = Post::macro_get_posts($params['category_id'], $params['page']+1);
        $html = '';
        /*
        foreach ($items as $item){
            $html .= view('blocks.slider.items.slider_product_item')->with([
                'item' => $item,
            ]);
        }*/
        $html .= view('blocks.blog.blog_section')->with([
            'posts' => $items,
        ]);


        $response['status'] = '1';
        $response['content'] = $html;
        $response['count'] = count($items);
        return json_encode($response,JSON_UNESCAPED_UNICODE);
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


    public function test_single(){
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

        //posts
        $posts = Post::query()
            ->where('status', '=', 'PUBLISHED')
            ->with("category")
            ->get();


        $styles = [
//            ["css/libs/flickity/flickity.min.css",false], // Do not delete
            ["css/blog.min.css",false],
//            ["css/home_buf.min.css",false]
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            ["runtime~js/blog.js",false],
            ["js/blog.js",false],
//            ["js/index_test.js",false],
        ];

        $html = view('includes/blog/single_page')->with([
            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'posts' => $posts,
            'meta' => $metadata,
            //'microdata' => $microdata_result,
        ]);
        return $html;
    }

}
