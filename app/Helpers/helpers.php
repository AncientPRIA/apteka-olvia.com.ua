<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\App;
use App\Processors\XML_Generator;
use App\Models\TranslatableString;
use TCG\Voyager\Facades\Voyager;
use Illuminate\Support\Facades\Cache;
use App\Models\Metadata;
//use App\Models\Category;

require_once "_debug.php";

function get_nav_menu($key = 'default'){
    switch($key){

        default:
            $arr = [
//                [
//                    'title' => 'АКЦИИ',
//                    'link' => route('discount_products'),
//                    'icon' => 'img/svg/procent.svg',
//                ],
                [
                    'title' => 'НАШИ АПТЕКИ',
                    'link' => route('shops_locations'),
                    'icon' => 'img/svg/geo-mark.svg',
                ],
                [
                    'title' => 'ТОВАРЫ',
                    'link' => route('products'),
                    'icon' => 'img/svg/drugs.svg',
                ],
                [
                    'title' => 'БЛОГ',
                    'link' => route('blog'),
                    'icon' => 'img/svg/blogging.svg',
                ],
                [
                    'title' => 'ВАКАНСИИ',
                    'link' => route('jobs'),
                    'icon' => 'img/svg/vacansy.svg',
                ],
//                [
//                    'title' => 'ПАРТНЁРАМ',
//                    'link' => route('partners'),
//                ],
                [
                    'title' => 'КОНТАКТЫ',
                    'link' => route('contact'),
                    'icon' => 'img/svg/phone.svg',
                ],

            ];
            return $arr;
            break;
    }
}


function user_verified(){
    $user = \Auth::user();
    if($user !== null){

        $is_verified = $user->hasVerifiedEmail();
        if($is_verified){
            return $user;
        }else{
            return false;
        }

        return $user;

    }else{
        return false;
    }
}

function generate_sitemap(){

    $sitemap_generator = new XML_Generator();
    $sitemap_generator->init_sitemap_xml();

    $sitemap_file_array = [];

    // Static
    $url = url('/');
    $last_mod = date('Y-m-d');
    $change_freq = 'monthly';
    $priority = '1.0';
    $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);

    $url = url('/contact/');
    $last_mod = date('Y-m-d');
    $change_freq = 'monthly';
    $priority = '1.0';
    $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);

    $url = url('/jobs/');
    $last_mod = date('Y-m-d');
    $change_freq = 'monthly';
    $priority = '1.0';
    $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);

    $url = url('/our_pharmacies/');
    $last_mod = date('Y-m-d');
    $change_freq = 'monthly';
    $priority = '1.0';
    $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);

    $url = url('/discount_products/');
    $last_mod = date('Y-m-d');
    $change_freq = 'monthly';
    $priority = '1.0';
    $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);

    $blog_base = $url = url('/blog/');
    $last_mod = date('Y-m-d');
    $change_freq = 'monthly';
    $priority = '1.0';
    $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);

    // Dynamic
    // Posts - Category, Post
    $nesting_posts = function($categories) use (&$sitemap_generator, &$nesting_posts, $blog_base){
        foreach (($categories ?? []) as $category){
            // Category
            $url = $blog_base . "/" . $category->get_path();
            $last_mod = date('Y-m-d', $category->updated_at->timestamp);
            $change_freq = 'monthly';
            $priority = '1.0';
            $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);

            // Posts
            $objects = $category->posts()->published()->get();
            foreach ($objects as $object){
                $url = $blog_base . "/" . $object->get_path();
                $last_mod = date('Y-m-d', $object->updated_at->timestamp);
                $change_freq = 'monthly';
                $priority = '1.0';
                $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);
            }

            $nesting_posts($category->children);
        }
    };
    $categories = Category::query()
        ->get();
    $nesting_posts($categories);

    $sitemap_file = public_path().'/sitemap_static.xml';
    $sitemap_file_link = url('/').'/sitemap_static.xml';
    $sitemap_file_array[] = $sitemap_file_link;
    $xml = $sitemap_generator->get_xml();
    file_put_contents($sitemap_file, $xml);

    $sitemap_generator = new XML_Generator();
    $sitemap_generator->init_sitemap_xml();

    $sitemap_max_size = 20000;
    $sitemap_index_file = 1;
    $sitemap_index = 0;

    // Products - Category, Product
    $nesting_products = function($categories) use (&$sitemap_generator, &$nesting_products, &$sitemap_max_size, &$sitemap_index_file, &$sitemap_index, &$sitemap_file_array){
        foreach (($categories ?? []) as $category){
            // Category
            $products = Product::macro_get_products($category->id, 1);
            if($products->total() === 0){
                continue;
            }
            $last_page = $products->lastPage();
            for ($i=1; $i<= $last_page; $i++ ){

                if($i === 1){
                    $url = url($category->get_path());
                }else{
                    $url = url($category->get_path())."?page=$i";
                }

                $last_mod = date('Y-m-d', $category->updated_at->timestamp);
                $change_freq = 'daily';
                $priority = '1.0';
                $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);
                $sitemap_index++;
            }

            $url = url($category->get_path());
            $last_mod = date('Y-m-d', $category->updated_at->timestamp);
            $change_freq = 'daily';
            $priority = '1.0';
            $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);
            $sitemap_index++;

            // Products
            $objects = $category->products()->published()->get();
            foreach ($objects as $object){

                if($sitemap_index >= $sitemap_max_size){

                    $sitemap_file = public_path().'/sitemap_'.$sitemap_index_file.'.xml';
                    $sitemap_file_link = url('/').'/sitemap_'.$sitemap_index_file.'.xml';
                    $sitemap_file_array[] = $sitemap_file_link;
                    $xml = $sitemap_generator->get_xml();
                    file_put_contents($sitemap_file, $xml);

                    $sitemap_index_file++;
                    $sitemap_index = 0;

                    $sitemap_generator = new XML_Generator();
                    $sitemap_generator->init_sitemap_xml();

                }

                $url = url($object->get_path());
                $last_mod = date('Y-m-d', $object->updated_at->timestamp);
                $change_freq = 'daily';
                $priority = '1.0';
                $sitemap_generator->add_sitemap_url($url, $last_mod, $change_freq, $priority);
                $sitemap_index++;


            }

            $nesting_products($category->children);
        }
    };
    $categories = ProductCategory::query()
        ->get();
    $nesting_products($categories);


    $sitemap_generator = new XML_Generator();
    $sitemap_generator->add_multi_sitemap($sitemap_file_array, $last_mod);


//    foreach($sitemap_file_array as $sitemap_file_array_item){
//        $url = $sitemap_file_array_item;
//        $last_mod = date('Y-m-d');
//        $sitemap_generator->add_multi_sitemap($url, $last_mod);
//    }

    $sitemap_file = public_path().'/sitemap.xml';
    $xml = $sitemap_generator->get_xml();
    file_put_contents($sitemap_file, $xml);

}

function get_strings(){
    $string_model = new TranslatableString();
    $strings = $string_model->get_all_key_value();
    return $strings;
}

// Get translated string. Safe function.
// Possible replaces:
// {year} = current year // not finished
function string(&$strings = null, $key, $default_value = ''){
    /*
    if($key === 'our services 1 body 3'){
        dd($strings);
    }
    */

    $strings = \Illuminate\Support\Facades\Config::get($strings);

    if(isset($strings[$key])){
        if($strings[$key] === 'null'){
            return '';
        }

        //$strings[$key] = str_replace('{year}', date('Y'), $strings[$key]);
        return $strings[$key];
    }else{
        $current_locale = \App::getLocale();
        Cache::forget('strings_'.$current_locale);

        $str = TranslatableString::query()
            ->where(['key' => $key])
            ->first()
        ;
        if($str === null){
            $str = new TranslatableString();
            $str->key = $key;
            $str->value = $default_value;
            $str->save();
        }

        /*
        TranslatableString::query()->insert([
            'key' => $key,
            'value' => $default_value,
        ]);
        */

        //return "NO_TRANSLATION!";
        $strings[$key] = "JUST INSERTED! RELOAD PAGE!";
        return $default_value;
    }
}

// Used in admin and front. Edit careful
function get_data_row_translated(){
    //$string_default
}

// Get options of $field from json of data_type (table data_types)
function get_data_options($data_type_id, $field){

    //$locales = config('voyager.multilingual.locales');
    $current_locale = \App::getLocale();

    //return Cache::remember($data_type_id.' '.$field.' '.$current_locale, 86400, function() use ($strings, $data_type_id, $field) {

    $data_rows = Voyager::model('DataRow')->where('data_type_id', '=', $data_type_id)->where('field', '=', $field)->first();
    if(isset($data_rows->details->options)){ // Options exists
        $details = $data_rows->details;

        if(isset($data_rows->details->strings_key)){ // Strings key exists

            $strings_key_base = $data_rows->details->strings_key;

            // Translate each option
            foreach ($details->options as $key=>$option){
                $string_key = $strings_key_base.'_'.$key;
                $string_default = $option;
                $translated_option = string($strings, $string_key, $string_default);
                $details->options->{$key} = $translated_option;
                /*
                if($translated_option !== ''){
                    $details->options->{$key} = $translated_option;
                }else{
                    $details->options->{$key} = $option;
                }
                */
            }
            return $details->options;

        }else{ // Strings key not exists, return untranslated options
            return $details->options;
        }

    }else{ // Options not exists, return empty object (wrong launch or options not set?)
        return new stdClass(); // false?
    }

    //});
}

// Make metadata
function get_meta($key, $or_object = null){
    if($key !== null AND $key !== ''){
        $metadata = Metadata::query()
            ->where('key', '=', $key)
            ->first();

        if($metadata !== null){
            $metadata = $metadata->translate(App::getLocale(), config('app.locale_front'));
            $meta['title'] = $metadata->meta_title;
            $meta['description'] = $metadata->meta_description;
            $meta['h1'] = $metadata->meta_h1;
            $meta['og_image'] = $metadata->image;
        }else{
            $metadata = new Metadata();
            $meta['title'] = '';
            $meta['description'] = '';
            $meta['h1'] = '';
            $meta['og_image'] = null;
            $metadata->key = $key;
            $metadata->meta_title = $meta['title'];
            $metadata->meta_description = $meta['description'];
            $metadata->meta_h1 = $meta['h1'];
            $metadata->image = $meta['og_image'];
            $metadata->save();
        }

    }elseif($or_object !== null){

        isset($or_object->meta_title) ? $meta['title'] = $or_object->meta_title : $meta['title'] = '';
        isset($or_object->description) ? $meta['description'] = $or_object->description : $meta['description'] = '';
        isset($or_object->meta_h1) ? $meta['h1'] = $or_object->meta_h1 : $meta['h1'] = '';
        if(isset($or_object->og_image)){
            $meta['og_image'] = $or_object->og_image;
        }else{
            if(isset($or_object->image)){
                $image = json_decode($or_object->image, true);
                if(is_array($image)){
                    $image = $image[0];
                }
                $meta['og_image'] = $image;
            }else{
                $meta['og_image'] = null;
            }
        }
        if($meta['og_image'] === null AND isset($or_object->image)) $meta['og_image'] = $or_object->og_image;

    }else{
        $meta['title'] = '';
        $meta['description'] = '';
        $meta['h1'] = '';
        $meta['og_image'] = null;
    }

    if($meta['og_image'] === null){
        $meta['og_image'] = 'opengraph/general.png';
    }

    return $meta;
}

// Strip phone string of brackets, spaces, etc.
function phone_strip($phone_string){
    return str_replace(['(', ')', ' ', '-', '+'], '', $phone_string);

}

// Prepare for search in json for $value of $key. Something like "name":"yoda"
function prepare_json_search($key, $value, $include_percent = true){
    $json_segment = '"'.$key.'":"'.$value.'"';
    if($include_percent){
        $json_segment = '%'.$json_segment.'%';
    }
    return $json_segment;
}

// Cut string and add $addition if $string less than $count
function excerpt($string, $count, $addition){

    $string = trim(strip_tags($string));
    if(mb_strlen($string) > $count){
        $charAtPosition = "";
        $string_length = mb_strlen($string);

        do {
            $count++;
            $charAtPosition = mb_substr($string, $count, 1);
        } while ($count < $charAtPosition && $charAtPosition != " ");

        return mb_substr($string, 0, $count) . $addition;
        //return mb_substr($string, 0, $count).$addition;
    }else{
        return $string;
    }
}

// Cleans string of unicode, ascii ... Use for body before insert
function clean_string($string){
    //$string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    //$string = preg_replace('/[\x00-\x1F\x7F]/', '', $string);
    //$string = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $string);
    //$string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $string);

    return $string;
}

// Search in column in 2-dimensional array
function find_in_array($array, $search, $column){
    $matches = [];
    //$search = 'Нитрокс';
    $search = mb_strtolower($search);
    foreach ($array as $row_index=>$row){
        if(isset($row[$column])){

            if(stripos( $row[$column], $search) !== false){
                $matches[] = $row;
            }
        }

    }
    return $matches;
}

// Saves (writes) params in config file
function save_config($items, $key)
{
    $path = explode('.', $key);
    $file = config_path($path[0]).'.php';
    if(!file_exists($file)){
        file_put_contents($file, '<?php return ' . var_export([], true) . ';');
    }
    $config = include($file);
    if(!is_array($config)){
        $config = [];
    }



    if(is_null($config)){
        //return false;
        $config = [];
    }

    foreach ($items as $key=>$value){
        $config[$key] = $value;
    }


    return file_put_contents($file, '<?php return ' . var_export($config, true) . ';');
}

function leading_symbols($string, $symbols, $desired_length = 6){
    return str_pad($string, $desired_length, $symbols, STR_PAD_LEFT);
}

// Delete folder and files recursively
function delete_folder($path) {
    if(file_exists($path)){
        if(is_dir($path)){
            $files = array_diff(scandir($path), array('.','..'));
            //$files = glob( $path . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned

            foreach( $files as $file ){
                delete_folder( $path."/".$file );
            }

            rmdir( $path );
        } elseif(is_file($path)) {
            unlink( $path );
        }
    }
}

// Get relative folder path from id, like "0-999/100-199" for id 106 and $thousands = true
function split_folders_by_id($id, $thousands = false){
    if($thousands === true){
        $folder_thousand = intdiv($id, 1000) * 1000;
        $folder_thousand = $folder_thousand.'-'.($folder_thousand+999);
        $folder_hundred = intdiv($id, 100) * 100;
        $folder_hundred = $folder_hundred.'-'.($folder_hundred+99);
        return $folder_thousand.'/'.$folder_hundred;
    }else{
        $folder_hundred = intdiv($id, 100) * 100;
        $folder_hundred = $folder_hundred.'-'.($folder_hundred+99);
        return $folder_hundred;
    }
}

// Get file from url and store in $folder (absolute) with name $file_name_wo_ext
// Returns filename with extension OR false
function download_image($url, $folder, $file_name_wo_ext){

    if(!is_dir($folder)) {
        mkdir($folder, 0755, true);
    }
    $info = pathinfo($url);
    $extension = $info['extension'];
    $full_filename = $folder .'/'. $file_name_wo_ext.'.'.$extension;


    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
    $data = curl_exec( $ch );
    curl_close( $ch );

    //$res = Storage::disk('public')->put($full_filename, $data, 'public');


    $res = file_put_contents($full_filename, $data);
    if($res !== false){
        return $file_name_wo_ext.'.'.$extension;
    }
    //$res = Image::make($url)->save($full_filename);

    return $res;
}