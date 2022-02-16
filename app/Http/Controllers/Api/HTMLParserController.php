<?php


namespace App\Http\Controllers\Api;
//$phantom_folder = '/home/priadigi/_phantom_js';
//require $phantom_folder.'/vendor/autoload.php';

use App\Http\Controllers\Controller;
use App\Models\ActiveSubstance;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Processors\HtmlParser;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\Post;
use Illuminate\Support\Facades\App;
use App\Models\TranslatableString;
use Illuminate\Support\Facades\Storage;
use App\Processors\CSV_Generator;

use SebastianBergmann\CodeCoverage\Report\PHP;
use Symfony\Component\DomCrawler\Crawler;

use JonnyW\PhantomJs\Client;

class HTMLParserController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function parse_konfeterra_ready(){
        echo '<pre>'.PHP_EOL;

        //$xls_path = base_path('data').'/konfeterra_2.xlsx';
        $xls_path = base_path('data').'/konfeterra_winestyle_matches2.xlsx';
        $xls_path_e = base_path('data').'/';

        Excel::load($xls_path, function($excel) {
            $excel->sheet(0, function($sheet){

                $site_url = "https://winestyle.com.ua";
                $image_base_path = public_path('uploads');
                //$csv_matches_path = base_path('data').'/konfeterra_winestyle_matches2.csv';
                $parser = new HtmlParser();
                //$csv_generator = new CSV_Generator();
                //$csv_generator->set_file($csv_matches_path);

                // Limits
                $start_items = 464; // 10 is first //5
                $limit_items = 50;
                $max_row = 484;

                // Stats
                $count_viewed = 0;
                $count_found = 0;
                $count_not_found = 0;
                $count_skipped = 0;
                $count_total = 0;

                $excel_products_arr = $sheet->toArray();

                //dd($excel_products_arr);

                for($excel_index = $start_items; $excel_index < count($excel_products_arr); $excel_index++){
                    // Empty rows skip
                    if($excel_products_arr[2] == null){
                        continue;
                    }
                    // Break max rows
                    if($excel_index >= $max_row){
                        break;
                    }
                    // Limits check
                    if($count_viewed >= $limit_items && $limit_items !== -1){
                        break;
                    }


                    // Process

                    $sku = $excel_products_arr[$excel_index][2];

//                    if($sku != 5387){
//                        continue;
//                    }

                    $source = $excel_products_arr[$excel_index][11];
                    echo PHP_EOL."--------- SKU ".$sku." index ".$excel_index.PHP_EOL;

                    // Skip verified with images
                    /*
                    if($excel_products_arr[$excel_index][0] === '*'){
                        $check_images_folder = $image_base_path.'/'.'konfeterra_ready/prod_'.$sku;
                        if(file_exists($check_images_folder) && is_dir($check_images_folder)){
                            echo "Images exists".PHP_EOL;
                            $count_viewed++;
                            continue;
                        }
                    }elseif($excel_products_arr[$excel_index][0] !== '0'){
                        echo "Skip other".PHP_EOL;
                        $count_viewed++;
                        continue;
                    }
                    */
                    // Skip all other except 0


                    // Skip not winestyle
                    if(strpos($source, 'winestyle.com.ua') === false){
                        echo "Not winestyle".PHP_EOL;
                        $count_viewed++;
                        continue;
                    }

                    // Skip with description
                    if($excel_products_arr[$excel_index][9] !== null && $excel_products_arr[$excel_index][9] !== ""){
                        echo "Have description".PHP_EOL;
                        $count_viewed++;
                        continue;
                    }

                    // --------------- Get single parts --------------- //
                    $html = $parser->get_get_content($source);
                    //$descriptions = $parser->get_doms($html, '.articles-container');
                    $parts = [
                        'desc_notes' => [
                            'path' => '.articles-container.notes .description-block',
                            'options' => [
                                'many' => true,
                                'text' => true,
                            ],
                        ],
                        'desc_interesting' => [
                            'path' => '.articles-container.desc .description-block',
                            'options' => [

                            ],
                        ],
                        'desc_manufacturer' => [
                            'path' => '.articles-container.manufacturer-desc .description-block',
                            'options' => [

                            ],
                        ],
                        'images' => [
                            'path' => '.left-aside .img-container.fancybox',
                            'options' => [

                            ],
                        ]
                    ];
                    $post_parts = $parser->get_parts($html, $parts, $site_url);

                    if(!is_array($post_parts['desc_notes'])){
                        $post_parts['desc_notes'] = [];
                    }
                    foreach ($post_parts['desc_notes'] as $note){
                        $rep = 0;
                        $tmp = preg_replace('/Цвет/', '', $note, -1, $rep);
                        if($rep > 0){
                            $site_item['info_color'] = trim($tmp);
                        }
                        $tmp = preg_replace('/Вкус/', '', $note, -1, $rep);
                        if($rep > 0){
                            $site_item['info_flavor'] = trim($tmp);
                        }
                        $tmp = preg_replace('/Аромат/', '', $note, -1, $rep);
                        if($rep > 0){
                            $site_item['info_scent'] = trim($tmp);
                        }
                        $tmp = preg_replace('/Гастрономические сочетания/', '', $note, -1, $rep);
                        if($rep > 0){
                            $site_item['info_gastro'] = trim($tmp);
                        }
                    }

                    $site_item['description'] = $post_parts['desc_interesting'];
                    if($site_item['description'] == null){
                        dd("No desc", $sku);
                    }
                    $site_item['desc_manufacturer'] = $post_parts['desc_manufacturer'];

                    $excel_products_arr[$excel_index][9] = $site_item['description'] ?? '';
                    $excel_products_arr[$excel_index][12] = $site_item['desc_manufacturer'] ?? '';
                    $excel_products_arr[$excel_index][13] = $site_item['info_color'] ?? '';
                    $excel_products_arr[$excel_index][14] = $site_item['info_flavor'] ?? '';
                    $excel_products_arr[$excel_index][15] = $site_item['info_scent'] ?? '';



                    //dd('stop', $site_item);
//                    $csv_generator->add_row([
//                        $excel_products_arr[0][$excel_index][0],
//                        $excel_products_arr[0][$excel_index][1],
//                        $excel_products_arr[0][$excel_index][2],
//                        $excel_products_arr[0][$excel_index][3],
//                        $excel_products_arr[0][$excel_index][4],
//                        $excel_products_arr[0][$excel_index][5],
//                        $excel_products_arr[0][$excel_index][6],
//                        $excel_products_arr[0][$excel_index][7],
//                        $site_item['description'],
//                        $excel_products_arr[0][$excel_index][9],
//                        $site_item['url'],
//                        $site_item['desc_manufacturer'] ?? '', // Manufacturer info
//                        $site_item['info_color'] ?? '', // Color
//                        $site_item['info_flavor'] ?? '', // Flavor
//                        $site_item['info_scent'] ?? '', // Scent
//                        $site_item['info_gastro'] ?? '', // Gastronomic combinations
//                    ]);

                    $img_counter = 0;

                    /*
                    if(!is_array($post_parts['images'])){
                        dd($post_parts);
                    }
                    foreach ($post_parts['images'] as $images_url){
                        $images_url = $images_url['url'];
                        $filepath = 'konfeterra_ready_2/prod_'.$sku;
                        $filename = 'img_'.$sku.'_'.$img_counter;
                        $result = $parser->download_image($images_url, $image_base_path.'/'.$filepath, $filename);

                        if($result !== false){
        //                    $product_image[] = $filepath.'/'.$result;
        //                    if($img_counter === 0){
        //                        $product_new->image_thumb = $filepath.'/'.$result;
        //                    }
                            $excel_products_arr[$excel_index][0] = $excel_products_arr[$excel_index][0]." GOT_IMG";
                        }else{
                            echo "image failed! (".$images_url.")".PHP_EOL;
                        }
        //                $product_new->image = json_encode($product_image, JSON_UNESCAPED_SLASHES);

                        $img_counter++;
                    }
                    */


                    $count_viewed++;
                }

                //$sheet->fromArray($results, null, 'A1', false, false);
                $sheet->fromArray($excel_products_arr, null, 'A1', false, false);
            });

        })->store('xlsx', $xls_path_e);

        echo '</pre>';
    }

    public function parse_winestyle(){
        echo '<pre>'.PHP_EOL;
        $site_url = "https://winestyle.com.ua";
        $image_base_path = public_path('uploads');
        $csv_matches_path = base_path('data').'/konfeterra_winestyle_matches2.csv';
        $xls_path = base_path('data').'/konfeterra_parsed_source.xlsx';
        $parser = new HtmlParser();
        $csv_generator = new CSV_Generator();
        $csv_generator->set_file($csv_matches_path);

        // Limits
        $start_items = 407; // 10 is first //5
        $limit_items = 50;

        // Stats
        $count_viewed = 0;
        $count_found = 0;
        $count_not_found = 0;
        $count_skipped = 0;
        $count_total = 0;


        $excel_products_arr = Excel::toArray(null, $xls_path);

        for($excel_index = $start_items; $excel_index < count($excel_products_arr[0]); $excel_index++){
            $source = $excel_products_arr[0][$excel_index][10];

            // Skip with source
            if($source !== null){
                $csv_generator->add_row([
                    $excel_products_arr[0][$excel_index][0],
                    $excel_products_arr[0][$excel_index][1],
                    $excel_products_arr[0][$excel_index][2],
                    $excel_products_arr[0][$excel_index][3],
                    $excel_products_arr[0][$excel_index][4],
                    $excel_products_arr[0][$excel_index][5],
                    $excel_products_arr[0][$excel_index][6],
                    $excel_products_arr[0][$excel_index][7],
                    $excel_products_arr[0][$excel_index][8],
                    $excel_products_arr[0][$excel_index][9],
                    $excel_products_arr[0][$excel_index][10],
                    '', // Manufacturer info
                    '', // Color
                    '', // Flavor
                    '', // Scent
                    '', // Gastronomic combinations
                ]);
                continue;
            }

            $count_viewed++;
            $re_search = false;
            if($count_viewed > $limit_items && $limit_items !== -1){
                break;
            }

            $sku = $excel_products_arr[0][$excel_index][1];

            // --------------- Searchname and volume --------------- //
            echo PHP_EOL."--------- SKU ".$sku." index ".$excel_index.PHP_EOL;

            $search_name = $full_name = $excel_products_arr[0][$excel_index][2];
            preg_match("/([0-9][\,\.][0-9]+\s*л)|([0-9]\s*л)|0\.75/", $search_name, $volume);
            if(count($volume) > 0){
                $volume = rtrim($volume[0], "л");
                $volume = str_replace(',', '.', $volume);
            }else{
                $count_skipped++;
                dd('volume', $volume);
                echo "SKIPPING".PHP_EOL;
                continue;
            }
            $search_name = preg_replace("/[0-9]+ лет|[0-9]+лет/", '', $search_name);
            $search_name = preg_replace("/([0-9][\,\.][0-9]+л)|([0-9]л)/", '', $search_name);
            $search_name = preg_replace("/[\(.+\)|[0-9,]+%/", '', $search_name); //13,5%
            $search_name = preg_replace("/\(.+\)/", '', $search_name); //(Молдова)
            $search_name = preg_replace("/п\/сух|п\/сл|сух\.|сухое/", '', $search_name);
            $search_name = preg_replace("/\s{2,}/", ' ', $search_name);

            $search_name = trim($search_name);

            // Take 3 words for search
            $tmp = explode(' ', $search_name);
            if(count($tmp) >= 3) {
                $search_name = $tmp[0] . " " . $tmp[1] . " " . $tmp[2];
            }

            //dd($sku);
            echo "FULL NAME: ".$full_name." --- SEARCH NAME: ".$search_name;
            if($volume !== ""){
                echo " --- VOLUME: ".$volume;
            }
            echo PHP_EOL;


            // --------------- Get searched items --------------- //
            //$url = "https://krasnoeibeloe.ru/catalog/?q=".urlencode($search_name);
            //$url = "https://winestyle.com.ua/remote.php?r=0.2758066658373841&w=loadmoreproducts&search_query=%D0%94%D0%B6%D0%B8%D0%BD+Beefeater&sort=productpopularity&searchlimit=0&capacityfilter=&pricegroup=&availability=&ajax=1";
            $url = "https://winestyle.com.ua/remote.php?w=loadmoreproducts&search_query=".urlencode($search_name)."&sort=productpopularity&searchlimit=0&capacityfilter=&pricegroup=&availability=&ajax=1";
            //dd($url);
            $html = $parser->get_get_content($url);
            dd($html);
            //dd($url, json_decode($html)->products);
            $html = json_decode($html)->products ?? "";
            dd($html);
            //$se = $parser->get_doms($html, "search-results");

//            $crawler = new Crawler();
//            $crawler->addHtmlContent($html, 'UTF-8');
//            $crawler = $crawler->filter('form.item-block .img-block');
//            dd($crawler);

            $params = [
                'urls' => [
                    'path' => '.item-block .img-block',
                    'options' => [
                        //'get_attributes' => 'style'
                    ],
                ]
            ];
            $search_items = $parser->get_parts($html, $params, $site_url);

            /*
            if($crawler->count() === 0){ // Not found
                dd("1st search not found", $html);
                $tmp = explode(' ', $search_name);
                if($tmp >= 3){
                    $search_name = $tmp[0]." ".$tmp[1]." ".$tmp[2];
                }else{
                    $count_not_found++;

                    echo "NOT_FOUND 1".PHP_EOL;
                    continue;
                }
                echo "SEARCH_NAME_#2: ".$search_name.PHP_EOL;
                $url = "https://krasnoeibeloe.ru/catalog/?q=".urlencode($search_name);
                $html = $parser->get_get_content($url);
                //$se = $parser->get_doms($html, "search-results");
                $crawler = new Crawler();
                $crawler->addHtmlContent($html, 'UTF-8');
                $crawler = $crawler->filter('search-results');
                if($crawler->count() === 0){ // Not found
                    $count_not_found++;
                    dd($html);
                    echo "NOT_FOUND 2".PHP_EOL;
                    continue;
                }
                //dd($html, $crawler);
                $tmp = $crawler->attr(':items');
                $search_items = json_decode($tmp);

                //$count_not_found++;
                //echo "NOT_FOUND".PHP_EOL;
                //continue;
            }else{
                $site_item = array();
            }
            dd($search_items);
            */

            /*
            if(count($search_items) === 0){
                $tmp = explode(' ', $search_name);
                if($tmp >= 3){
                    $search_name = $tmp[0]." ".$tmp[1]." ".$tmp[2];
                }else{
                    $count_not_found++;
                    echo "NOT_FOUND 3".PHP_EOL;
                    continue;
                }
                echo "SEARCH_NAME_#2: ".$search_name.PHP_EOL;
                $url = "https://krasnoeibeloe.ru/catalog/?q=".urlencode($search_name);
                $html = $parser->get_get_content($url);
                //$se = $parser->get_doms($html, "search-results");
                $crawler = new Crawler();
                $crawler->addHtmlContent($html, 'UTF-8');
                $crawler = $crawler->filter('search-results');
                if($crawler->count() === 0){ // Not found
                    $count_not_found++;
                    echo "NOT_FOUND 4".PHP_EOL;
                    continue;
                }
                //dd($html, $crawler);
                $tmp = $crawler->attr(':items');
                $search_items = json_decode($tmp);
            }
            */

            // Filter items from search
            if(!is_array($search_items['urls'])){
                $count_not_found++;
                $csv_generator->add_row([
                    $excel_products_arr[0][$excel_index][0],
                    $excel_products_arr[0][$excel_index][1],
                    $excel_products_arr[0][$excel_index][2],
                    $excel_products_arr[0][$excel_index][3],
                    $excel_products_arr[0][$excel_index][4],
                    $excel_products_arr[0][$excel_index][5],
                    $excel_products_arr[0][$excel_index][6],
                    $excel_products_arr[0][$excel_index][7],
                    $excel_products_arr[0][$excel_index][8],
                    $excel_products_arr[0][$excel_index][9],
                    $excel_products_arr[0][$excel_index][10],
                    '', // Manufacturer info
                    '', // Color
                    '', // Flavor
                    '', // Scent
                    '', // Gastronomic combinations
                ]);
                echo "NOT_FOUND".PHP_EOL;
                continue;
            }
            foreach ($search_items['urls'] as $search_item){

                if(!isset($search_item['url'])){
                    dd('???', $search_item);
                }

                $site_item['url'] = $search_item['url'];

                /*
                if($volume !== "" &&  $search_item->volume === $volume){
                    $site_item['precise'] = true;
                }else{
                    $site_item['precise'] = false;
                }
                */
                break;
            }
            if(!isset($site_item['url'])){
                $count_not_found++;
                $csv_generator->add_row([
                    $excel_products_arr[0][$excel_index][0],
                    $excel_products_arr[0][$excel_index][1],
                    $excel_products_arr[0][$excel_index][2],
                    $excel_products_arr[0][$excel_index][3],
                    $excel_products_arr[0][$excel_index][4],
                    $excel_products_arr[0][$excel_index][5],
                    $excel_products_arr[0][$excel_index][6],
                    $excel_products_arr[0][$excel_index][7],
                    $excel_products_arr[0][$excel_index][8],
                    $excel_products_arr[0][$excel_index][9],
                    $excel_products_arr[0][$excel_index][10],
                    '', // Manufacturer info
                    '', // Color
                    '', // Flavor
                    '', // Scent
                    '', // Gastronomic combinations
                ]);
                echo "NOT_FOUND".PHP_EOL;
                continue;
            }
            echo "FOUND: ".$site_item['url'];
            $count_found++;

            // --------------- Get single parts --------------- //
            $html = $parser->get_get_content($site_item['url']);
            //$descriptions = $parser->get_doms($html, '.articles-container');
            $parts = [
                'desc_notes' => [
                    'path' => '.articles-container.notes .description-block',
                    'options' => [
                        'many' => true,
                        'text' => true,
                    ],
                ],
                'desc_interesting' => [
                    'path' => '.articles-container.desc .description-block',
                    'options' => [

                    ],
                ],
                'desc_manufacturer' => [
                    'path' => '.articles-container.manufacturer-desc .description-block',
                    'options' => [

                    ],
                ],
                'images' => [
                    'path' => '.left-aside .img-container.fancybox',
                    'options' => [

                    ],
                ]
            ];
            $post_parts = $parser->get_parts($html, $parts, $site_url);

            if(!is_array($post_parts['desc_notes'])){
                $post_parts['desc_notes'] = [];
            }
            foreach ($post_parts['desc_notes'] as $note){
                $rep = 0;
                $tmp = preg_replace('/Цвет/', '', $note, -1, $rep);
                if($rep > 0){
                    $site_item['info_color'] = trim($tmp);
                }
                $tmp = preg_replace('/Вкус/', '', $note, -1, $rep);
                if($rep > 0){
                    $site_item['info_flavor'] = trim($tmp);
                }
                $tmp = preg_replace('/Аромат/', '', $note, -1, $rep);
                if($rep > 0){
                    $site_item['info_scent'] = trim($tmp);
                }
                $tmp = preg_replace('/Гастрономические сочетания/', '', $note, -1, $rep);
                if($rep > 0){
                    $site_item['info_gastro'] = trim($tmp);
                }
            }

            $site_item['description'] = $post_parts['desc_interesting'];
            $site_item['desc_manufacturer'] = $post_parts['desc_manufacturer'];

            //dd('stop', $site_item);
            $csv_generator->add_row([
                $excel_products_arr[0][$excel_index][0],
                $excel_products_arr[0][$excel_index][1],
                $excel_products_arr[0][$excel_index][2],
                $excel_products_arr[0][$excel_index][3],
                $excel_products_arr[0][$excel_index][4],
                $excel_products_arr[0][$excel_index][5],
                $excel_products_arr[0][$excel_index][6],
                $excel_products_arr[0][$excel_index][7],
                $site_item['description'],
                $excel_products_arr[0][$excel_index][9],
                $site_item['url'],
                $site_item['desc_manufacturer'] ?? '', // Manufacturer info
                $site_item['info_color'] ?? '', // Color
                $site_item['info_flavor'] ?? '', // Flavor
                $site_item['info_scent'] ?? '', // Scent
                $site_item['info_gastro'] ?? '', // Gastronomic combinations
            ]);

            $img_counter = 0;
            /*
            foreach ($post_parts['images'] as $images_url){
                $images_url = $images_url['url'];
                $filepath = 'konfeterra_2/prod_'.$sku;
                $filename = 'img_'.$sku.'_'.$img_counter;
                $result = $parser->download_image($images_url, $image_base_path.'/'.$filepath, $filename);

                if($result !== false){
//                    $product_image[] = $filepath.'/'.$result;
//                    if($img_counter === 0){
//                        $product_new->image_thumb = $filepath.'/'.$result;
//                    }
                }else{
                    echo "image failed! (".$images_url.")".PHP_EOL;
                }
//                $product_new->image = json_encode($product_image, JSON_UNESCAPED_SLASHES);

                $img_counter++;
            }
            */

        }

        // End
        echo PHP_EOL;
        echo "COUNT VIEWED: ".$count_viewed.PHP_EOL;
        echo "COUNT FOUND: ".$count_found.PHP_EOL;
        echo "COUNT NOT FOUND: ".$count_not_found.PHP_EOL;
        echo "COUNT SKIPPED: ".$count_skipped.PHP_EOL;
        echo "COUNT TOTAL: ".$count_total.PHP_EOL;


        echo '</pre>';
    }

    public function parse_krasnoeibeloe(){
        echo '<pre>'.PHP_EOL;



        // --------------- Init --------------- //
        $site_url = "https://krasnoeibeloe.ru";
        $image_base_path = public_path('uploads');
        $csv_matches_path = base_path('data').'/konfeterra_matches.csv';
        $xls_path = base_path('data').'/konfeterra_products_list.xls';
        $parser = new HtmlParser();
        $csv_generator = new CSV_Generator();
        $csv_generator->set_file($csv_matches_path);
//        $csv_generator->add_row(["Номенклатура", "Артикул", "Полное наименование", "Цена розница",
//            "Тип цен", "Цена дисконт 5%", "Штрихкод", "Количество",
//            "Описание", "Алкоголь", "Источник"
//        ]);


        // Limits
        $start_items = 1199; // 10 is first //5
        $limit_items = 200;

        // Stats
        $count_viewed = 0;
        $count_found = 0;
        $count_not_found = 0;
        $count_skipped = 0;
        $count_total = 0;


        $excel_products_arr = Excel::toArray(null, $xls_path);
        //dd($excel_products_arr);

        for($excel_index = $start_items; $excel_index < count($excel_products_arr[0]); $excel_index++){
            $count_viewed++;
            $re_search = false;
            if($count_viewed > $limit_items && $limit_items !== -1){
                break;
            }

            // --------------- Searchname and volume --------------- //
            echo PHP_EOL."---------".PHP_EOL;

            $search_name = $full_name = $excel_products_arr[0][$excel_index][4];
            preg_match("/([0-9][\,\.][0-9]+л)|([0-9]л)/", $search_name, $volume);
            if(count($volume) > 0){
                $volume = rtrim($volume[0], "л");
                $volume = str_replace(',', '.', $volume);
            }else{
                $count_skipped++;
                echo "SKIPPING".PHP_EOL;
                continue;
            }
            $search_name = preg_replace("/[0-9]+ лет|[0-9]+лет/", '', $search_name);
            $search_name = preg_replace("/([0-9][\,\.][0-9]+л)|([0-9]л)/", '', $search_name);
            $search_name = preg_replace("/[\(.+\)|[0-9]+%/", '', $search_name); // (fff), 40%

            $search_name = trim($search_name);
            $sku = $excel_products_arr[0][$excel_index][3];
            echo "FULL NAME: ".$full_name." --- SEARCH NAME: ".$search_name;
            if($volume !== ""){
                echo " --- VOLUME: ".$volume;
            }
            echo PHP_EOL;


            // --------------- Get searched items --------------- //
            $url = "https://krasnoeibeloe.ru/catalog/?q=".urlencode($search_name);
            $html = $parser->get_get_content($url);
            //$se = $parser->get_doms($html, "search-results");
            $crawler = new Crawler();
            $crawler->addHtmlContent($html, 'UTF-8');
            $crawler = $crawler->filter('search-results');
            if($crawler->count() === 0){ // Not found
                $tmp = explode(' ', $search_name);
                if($tmp >= 3){
                    $search_name = $tmp[0]." ".$tmp[1]." ".$tmp[2];
                }else{
                    $count_not_found++;
                    echo "NOT_FOUND".PHP_EOL;
                    continue;
                }
                echo "SEARCH_NAME_#2: ".$search_name.PHP_EOL;
                $url = "https://krasnoeibeloe.ru/catalog/?q=".urlencode($search_name);
                $html = $parser->get_get_content($url);
                //$se = $parser->get_doms($html, "search-results");
                $crawler = new Crawler();
                $crawler->addHtmlContent($html, 'UTF-8');
                $crawler = $crawler->filter('search-results');
                if($crawler->count() === 0){ // Not found
                    $count_not_found++;
                    echo "NOT_FOUND".PHP_EOL;
                    continue;
                }
                //dd($html, $crawler);
                $tmp = $crawler->attr(':items');
                $search_items = json_decode($tmp);

                //$count_not_found++;
                //echo "NOT_FOUND".PHP_EOL;
                //continue;
            }else{
                $tmp = $crawler->attr(':items');
                $search_items = json_decode($tmp);
                $site_item = array();
            }

            if(count($search_items) === 0){
                $tmp = explode(' ', $search_name);
                if($tmp >= 3){
                    $search_name = $tmp[0]." ".$tmp[1]." ".$tmp[2];
                }else{
                    $count_not_found++;
                    echo "NOT_FOUND".PHP_EOL;
                    continue;
                }
                echo "SEARCH_NAME_#2: ".$search_name.PHP_EOL;
                $url = "https://krasnoeibeloe.ru/catalog/?q=".urlencode($search_name);
                $html = $parser->get_get_content($url);
                //$se = $parser->get_doms($html, "search-results");
                $crawler = new Crawler();
                $crawler->addHtmlContent($html, 'UTF-8');
                $crawler = $crawler->filter('search-results');
                if($crawler->count() === 0){ // Not found
                    $count_not_found++;
                    echo "NOT_FOUND".PHP_EOL;
                    continue;
                }
                //dd($html, $crawler);
                $tmp = $crawler->attr(':items');
                $search_items = json_decode($tmp);
            }

            foreach ($search_items as $search_item){
                //if($search_item->where !== 'desc'){

                    if(!isset($search_item->url)){
                        dd($search_item);
                    }

                    if(!isset($search_item->alcohol) && !isset($search_item->volume) && $search_item->where === 'desc'){
                        continue;
                    }

                    $site_item['url'] = $search_item->url;
                    $site_item['alcohol'] = $search_item->alcohol ?? null;
                    //$site_item['manufacturer'] = $search_item->proizvoditel;

                    if($volume !== "" &&  $search_item->volume === $volume){
                        $site_item['precise'] = true;
                    }else{
                        $site_item['precise'] = false;
                    }
                    break;
                //}
            }
            if(!isset($site_item['url'])){
                $count_not_found++;
                echo "NOT_FOUND".PHP_EOL;
                continue;
            }
            if(!isset($site_item['alcohol'])){
                echo "NOT_ALCOHOL".PHP_EOL;
                continue;
            }
            echo "FOUND: ".$site_item['url'];
            $count_found++;

            // --------------- Get single parts --------------- //
            $html = $parser->get_get_content($site_url.$site_item['url']);
            $parts = [
                'description' => [
                    'path' => '.pr_card_descr_visible',
                    'options' => [

                    ],
                ],
                'images' => [
                    'path' => '.pr_card_images_slide .pr_card_img a',
                    'options' => [
                        //'get_attributes' => 'style'
                    ],
                ]
            ];
            $post_parts = $parser->get_parts($html, $parts, $site_url);
            $site_item['description'] = $post_parts['description'];
            //dd($post_parts);

            $csv_generator->add_row([
                $excel_products_arr[0][$excel_index][0],
                $excel_products_arr[0][$excel_index][3],
                $excel_products_arr[0][$excel_index][4],
                $excel_products_arr[0][$excel_index][6],
                $excel_products_arr[0][$excel_index][7],
                $excel_products_arr[0][$excel_index][8],
                $excel_products_arr[0][$excel_index][9],
                $excel_products_arr[0][$excel_index][10],
                $site_item['description'],
                $site_item['alcohol'],
                $site_item['url'],
            ]);

            $img_counter = 0;
            foreach ($post_parts['images'] as $images_url){
                $images_url = $images_url['url'];
                $filepath = 'konfeterra/prod_'.$sku;
                $filename = 'img_'.$sku.'_'.$img_counter;
                $result = $parser->download_image($images_url, $image_base_path.'/'.$filepath, $filename);

                if($result !== false){
//                    $product_image[] = $filepath.'/'.$result;
//                    if($img_counter === 0){
//                        $product_new->image_thumb = $filepath.'/'.$result;
//                    }
                }else{
                    echo "image failed! (".$images_url.")".PHP_EOL;
                }
//                $product_new->image = json_encode($product_image, JSON_UNESCAPED_SLASHES);

                $img_counter++;
            }

        }

        // End
        echo PHP_EOL;
        echo "COUNT VIEWED: ".$count_viewed.PHP_EOL;
        echo "COUNT FOUND: ".$count_found.PHP_EOL;
        echo "COUNT NOT FOUND: ".$count_not_found.PHP_EOL;
        echo "COUNT SKIPPED: ".$count_skipped.PHP_EOL;
        echo "COUNT TOTAL: ".$count_total.PHP_EOL;


        echo '</pre>';
    }

    public function parse_apteka911(){
        echo '<pre>'.PHP_EOL;


        $image_base_path = public_path('uploads');
        $parser = new HtmlParser();

        $item_count = 0;
        $item_limit = -1;
        $page_counter = 1;  // Starting page
        $page_limit = -1;    // Max ending page

        $cat_index = 19;

        $tmp_distinct_who_allowed = [];

        /*
        $searched_items = $parser->get_urls($html, 'body .block-groups-list > ul > li > a');
            //$parser->get_doms($html, 'body .block-groups-list > ul > li > a');
        dd($searched_items);
        */

        $cats_urls = [
            0 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/prostuda_i_gripp",
            1 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/serdtse_sosudyi_krov",
            2 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/pischevaritelnyiy_trakt",
            3 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/obezbolivayuschie",
            4 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/mochepolovaya_sistema",
            5 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/protivomikrobnyie",
            6 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/ot_parazitov",
            7 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/dlya_nervnoy_sistemyi",
            8 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/immunitet",
            9 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/vitaminyi_i_dobavki",
            10 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/kostno_myishechnaya_sistema",
            11 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/ot_diabeta",
            12 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/protivoopuholevyie",
            13 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/dermatologiya",
            14 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/rastvoryi",
            15 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/lechenie_allergii",
            16 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/lechenie_varikoza_i_gemorroya",
            17 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/lechenie_glaz_i_ushey",
            18 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/lechenie_zabolevaniy_polosti_rta",
            19 => "https://apteka911.com.ua/shop/lekarstvennyie-preparatyi/gormonalnyie_preparatyi",
        ];

        $cat_url = $url = $cats_urls[$cat_index];
        $page_segment = '';

        $html = $parser->get_get_content($url);
        //dd($html);
        $pagination = $parser->get_doms($html, 'ul.pagination > li > a');
        $tmp_count = count($pagination);
        $max_page = (int)$pagination[$tmp_count-2];

        $is_first_page = true;
        for (; $page_counter <= $max_page && ($page_counter <= $page_limit || $page_limit === -1); $page_counter++){
            if($is_first_page){
                $category_page_html = $html;
                $is_first_page = false;
            }else{
                $page_segment = '/page='.$page_counter;
                $url = $cat_url . $page_segment;
                $category_page_html = $parser->get_get_content($url);
            }

            // ul.block-prod_tiles div.block-prod .b-prod__thumb
            $searched_items = $parser->get_urls($category_page_html, '.block-prod_tiles div.block-prod .b-prod__thumb');

            foreach ($searched_items as $searched_item){
                if($item_count >= $item_limit and $item_limit !== -1){
                    break;
                }
                // Link to post
                //$product_url = $parser->get_urls($searched_item, 'a')[0];
                $product_url = $searched_item;

                $product_new = Product::where('parsed_source', '=', $product_url)
                    ->first();
                if($product_new !== null){
                    continue;
                }
                $product_new = new Product();

                echo $product_url.PHP_EOL;
                //dd($product_url, $post_parts, $parameters, $images_urls);

                $searched_item_content = $parser->get_get_content($product_url);

                $parts = [
                    'title'     => [
                        'path' => '#wrp-content .product-head-instr > h1',
                    ],
                    'price'     => [
                        'path' => '#wrp-content .card-price .price-new',
                        'options' => [
                            'get_attributes' => 'content'
                        ],
                    ],
                    'parameters' => [
                        'path' => '#wrp-content .parameter-table td',
                        'options' => [
                            'many' => true,
                        ],
                    ],
                    'content_text' => [
                        'path' => '#wrp-content .symptom-content > div',
                        'options' => [
                            'many' => true,
                        ],
                    ]
                ];



                $post_parts = $parser->get_parts($searched_item_content, $parts);

                $parameters = [];
                for($i = 0; $i < count($post_parts['parameters']); $i+=2){
                    $type = strip_tags($post_parts['parameters'][$i]);
                    switch ($type){
                        // text in link
                        case 'Торговое название':
                        case 'Действующие вещества':
                        case 'Код АТС':
                            $value = $parser->get_doms($post_parts['parameters'][$i+1], 'a');
                            break;

                        case 'Категория':
                            $value = $parser->get_doms($post_parts['parameters'][$i+1], 'p');

                            foreach ($value as $key=>$item){
                                $tmp = explode('&gt;', strip_tags($item));
                                foreach($tmp as $tmp_key =>$tmp_value){
                                    $tmp[$tmp_key] = trim($tmp_value);
                                }
                                $value[$key] = $tmp;
                            }
                            break;

                        // list
                        case 'Кому можно':
                            $value = $parser->get_parts($post_parts['parameters'][$i+1], [
                                'items' => [
                                    'path' => 'ul li div',
                                    'options' => [
                                        'many' => true,
                                        'text' => true,
                                    ],
                                ]
                            ]);
                            $allowed = [];
                            for($j=0; $j < count($value['items']) -1; $j+=2){
                                $allowed_to = $value['items'][$j];
                                $allowed_value = $value['items'][$j+1];
                                $allowed[$allowed_to] = $allowed_value;
                            }
                            $value = $allowed;

                            break;

                        // text
                        case 'Форма выпуска':
                        case 'Способ введения':
                        case 'Вид упаковки':
                        case 'Количество в упаковке':
                        case 'Взаимодействие с едой':
                        case 'Чувствительность к свету':
                        case 'Условия отпуска':
                        case 'Срок годности':
                        case 'Температура хранения':
                        case 'Признак отечественный':
                        case 'Производитель':
                        case 'Страна владелец лицензии':
                            $value = strip_tags(trim($post_parts['parameters'][$i+1]));
                            break;

                        // skip
                        case 'Кол-во действ. вещ-ва':
                            break;

                        default:
                            dd('UNKNOWN TYPE! ', $type);
                            break;
                    }
                    $parameters[$type] = [
                        'type' => $type,
                        'value' => $value, // array
                    ];

                }
                //dd($product_url, $parameters);

                // Splitting headers and texts if needed
                //$post_parts['content_text'][0] = trim(str_replace(['<p>', '</p>'], '', $post_parts['content_text'][0]));
                //preg_match_all('/(?:<h2>)(.+)(?:<\/h2>)/', $post_parts['content_text'][0], $headers);
                //preg_match_all('/<\/h2>(.+?)(?:<h2>|$)/s', $post_parts['content_text'][0], $texts);
                //dd($headers[1], $texts[1]);

                // Images
                /*
                $curl_post = [
                    'related' => 1,
                    'id' => 563,
                    'code' => 2598,
                ];
                $curl = curl_init('https://apteka911.com.ua/mmi');
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                //curl_setopt($curl, CURLOPT_HTTPHEADER, '');
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($curl);
                $result = json_decode($result);
                */

                preg_match('/{"cart":.*}\n/', $searched_item_content, $thumbs);
                $thumbs = json_decode($thumbs[0], true);

                $images_urls = [];
                $base_img_url = $thumbs['product']['dataUrl'].'photos';
                //$images_urls[] = $base_img_url.$thumbs['product']['productThumbs']['big']['file'];
                foreach ($thumbs['photos'] as $source_photo){
                    $images_urls[] = $base_img_url.'/'.$source_photo['photoBigFile'];
                }

                /*
                'trade_title' => 'Торговое название',
                'active_substance_id' => '',
                */

                // DB stuff
                try{
                    // Debug
                    /*
                    if($product_url === 'https://apteka911.com.ua/shop/septefril-darnitsa-tabl-0-2mg-20-p35117'){
                        dd($post_parts);
                    }
                    */

                    // Reqs
                    $price = $post_parts['price'][1] ?: $post_parts['price'][2] ?: $post_parts['price'][3] ?: 0;
                    $product_new->title = $post_parts['title'];
                    $product_new->status = 'PUBLISHED';
                    $product_new->price = $price;
                    $product_new->excerpt = excerpt($post_parts['content_text'][0], 120, '...');
                    $product_new->author_id = 1;
                    $product_new->save();


                    // Categories
                    //$categories = [];
                    $is_first_category = true;
                    if(isset($parameters['Категория']['value'])){
                        foreach ($parameters['Категория']['value'] as $parameter_category_chain){
                            // root category
                            $product_category = ProductCategory::where('name', '=', $parameter_category_chain[0])
                                ->where('parent_id', '=', null)
                                ->first();
                            if($product_category === null){
                                $product_category = new ProductCategory();
                                $product_category->name = $parameter_category_chain[0];
                                $product_category->save();
                            }
                            // child category
                            if(isset($parameter_category_chain[1])){
                                //dd($parameter_category_chain[1], $product_category->id);
                                $product_category_child = ProductCategory::where('name', '=', $parameter_category_chain[1])
                                    ->where('parent_id', '=', $product_category->id)
                                    ->first();
                                if($product_category_child === null){
                                    $product_category_child = new ProductCategory();
                                    $product_category_child->name = $parameter_category_chain[1];
                                    $product_category_child->parent_id = $product_category->id;
                                    $product_category_child->save();
                                }
                            }else{
                                $product_category_child = null;
                            }
                            //dd($product_category, $parameter_category_chain, $product_category_child);

                            // assign to product
                            if($is_first_category){
                                $product_new->category_id = $product_category_child->id ?? $product_category->id;
                            }else{
                                $product_new->categories()->attach($product_category_child->id ?? $product_category->id);
                            }


                            $is_first_category = false;

                        }
                    }

                    // Active substances
                    if(isset($parameters['Действующие вещества']['value'])){
                        foreach ($parameters['Действующие вещества']['value'] as $parameter_active_substance){
                            $active_substance = ActiveSubstance::where('title', '=', $parameter_active_substance)
                                ->first();
                            if($active_substance === null){
                                $active_substance = new ActiveSubstance();
                                $active_substance->title = $parameter_active_substance;
                                $active_substance->save();
                            }

                            // assign to product
                            $product_new->active_substances()->attach($active_substance->id);
                        }
                    }

                    // Allowed to
                    if(isset($parameters['Кому можно']['value'])){
                        foreach ($parameters['Кому можно']['value'] as $parameter_allowed_key=>$parameter_allowed_value){
                            switch ($parameter_allowed_key){
                                case 'Взрослым':
                                    $product_new->allowed_adult = $parameter_allowed_value;
                                    break;
                                case 'Детям':
                                    $product_new->allowed_child = $parameter_allowed_value;
                                    break;
                                case 'Беременным':
                                    $product_new->allowed_pregnant = $parameter_allowed_value;
                                    break;
                                case 'Кормящим':
                                    $product_new->allowed_nursing = $parameter_allowed_value;
                                    break;
                                case 'Аллергикам':
                                    $product_new->allowed_allergic = $parameter_allowed_value;
                                    break;
                                case 'Диабетикам':
                                    $product_new->allowed_diabetic = $parameter_allowed_value;
                                    break;
                                case 'Водителям':
                                    $product_new->allowed_driver = $parameter_allowed_value;
                                    break;
                                default:
                                    throw new \Exception('UNKNOWN ALLOWED TYPE! '.$parameter_allowed_key);
                                    break;
                            }
                        }
                    }

                    if(isset($parameters['Торговое название']['value'][0])){
                        $product_new->title_short = $parameters['Торговое название']['value'][0];
                    }
                    if(isset($parameters['Форма выпуска']['value'])){
                        $product_new->release_form = $parameters['Форма выпуска']['value'];
                    }
                    if(isset($parameters['Способ введения']['value'])){
                        $product_new->administration = $parameters['Способ введения']['value'];
                    }
                    if(isset($parameters['Вид упаковки']['value'])){
                        $product_new->packaging_type = $parameters['Вид упаковки']['value'];
                    }
                    if(isset($parameters['Количество в упаковке']['value'])){
                        $product_new->count_in_package = $parameters['Количество в упаковке']['value'];
                    }
                    if(isset($parameters['Взаимодействие с едой']['value'])){
                        $product_new->food_interaction = $parameters['Взаимодействие с едой']['value'];
                    }
                    if(isset($parameters['Чувствительность к свету']['value'])){
                        $product_new->light_sensitivity = $parameters['Чувствительность к свету']['value'];
                    }
                    if(isset($parameters['Условия отпуска']['value'])){
                        $product_new->conditions_of_supply = $parameters['Условия отпуска']['value'];
                    }
                    if(isset($parameters['Срок годности']['value'])){
                        $product_new->expiration_date = $parameters['Срок годности']['value'];
                    }
                    if(isset($parameters['Температура хранения']['value'])){
                        $product_new->storage_temperature = $parameters['Температура хранения']['value'];
                    }
                    if(isset($parameters['Производитель']['value'])){
                        $product_new->manufacturer = $parameters['Производитель']['value'];
                    }
                    if(isset($parameters['Страна владелец лицензии']['value'])){
                        $product_new->country_license_holder = $parameters['Страна владелец лицензии']['value'];
                    }
                    if(isset($parameters['Код АТС']['value'][0])){
                        $product_new->atc_code = $parameters['Код АТС']['value'][0];
                    }

                    $product_new->body = clean_string($post_parts['content_text'][2]);
                    $product_new->parsed_source = $product_url;

                    $product_image = [];
                    $img_counter = 0;
                    foreach ($images_urls as $images_url){
                        $filename = 'prod_'.$product_new->id.'_'.$img_counter;
                        $result = $parser->download_image($images_url, $image_base_path.'/products', $filename);

                        if($result !== false){
                            $product_image[] = 'products/'.$result;
                            if($img_counter === 0){
                                $product_new->image_thumb = 'products/'.$result;
                            }
                        }else{
                            echo "image failed! (".$images_url.")".PHP_EOL;
                        }
                        $product_new->image = json_encode($product_image, JSON_UNESCAPED_SLASHES);

                        $img_counter++;
                    }


                    $product_new->save();
                }catch (\Exception $exception){
                    $product_new->delete();
                    dd($exception->getMessage());
                }




                $item_count++;
            }
            echo "PAGE FINISHED".PHP_EOL;

        }

        echo 'FINISHED. $item_count = '.$item_count.PHP_EOL;
        echo "</pre>";
    }

    public function parse_eapteka(){
        //echo '<pre>'.PHP_EOL;


        $image_base_path = public_path('uploads');
        $parser = new HtmlParser();

        $item_count = 0;
        $item_limit = -1;
        $page_counter = 1;  // Starting page
        $page_limit = -1;    // Max ending page

        //$cat_index = 19;

        $tmp_distinct_who_allowed = [];

        /*
        $searched_items = $parser->get_urls($html, 'body .block-groups-list > ul > li > a');
            //$parser->get_doms($html, 'body .block-groups-list > ul > li > a');
        dd($searched_items);
        */












        $cats_urls = [
            'Лекарства' => 'https://www.eapteka.ru/goods/drugs/',
            'Витамины и БАД' => 'https://www.eapteka.ru/goods/vitaminy_i_bad/',
            'Красота' => 'https://www.eapteka.ru/goods/beauty/',
            'Гигиена' => 'https://www.eapteka.ru/goods/gigiena/',
            'Линзы' => 'https://www.eapteka.ru/goods/linzy/',
            'Мать и дитя' => 'https://www.eapteka.ru/goods/mother/',
            'Медтовары' => 'https://www.eapteka.ru/goods/medical/',
            'Интим' => 'https://www.eapteka.ru/goods/intimnye_tovary/',
            'Зоотовары' => 'https://www.eapteka.ru/goods/zootovary/',
            'Медтехника' => 'https://www.eapteka.ru/goods/pribory_i_meditsinskaya_tekhnika/',
        ];

        foreach ($cats_urls as $cat_level_1_name => $cat_level_1_url){
            $config = [
                'url' => $cat_level_1_url,

            ];
            $result = $this->get_archive($config);
        }


        //dd($html);
        $pagination = $parser->get_doms($html, 'ul.pagination > li > a');
        $tmp_count = count($pagination);
        $max_page = (int)$pagination[$tmp_count-2];

        $is_first_page = true;
        for (; $page_counter <= $max_page && ($page_counter <= $page_limit || $page_limit === -1); $page_counter++){
            if($is_first_page){
                $category_page_html = $html;
                $is_first_page = false;
            }else{
                $page_segment = '/page='.$page_counter;
                $url = $cat_url . $page_segment;
                $category_page_html = $parser->get_get_content($url);
            }

            // ul.block-prod_tiles div.block-prod .b-prod__thumb
            $searched_items = $parser->get_urls($category_page_html, '.block-prod_tiles div.block-prod .b-prod__thumb');

            foreach ($searched_items as $searched_item){
                if($item_count >= $item_limit and $item_limit !== -1){
                    break;
                }
                // Link to post
                //$product_url = $parser->get_urls($searched_item, 'a')[0];
                $product_url = $searched_item;

                $product_new = Product::where('parsed_source', '=', $product_url)
                    ->first();
                if($product_new !== null){
                    continue;
                }
                $product_new = new Product();

                echo $product_url.PHP_EOL;
                //dd($product_url, $post_parts, $parameters, $images_urls);

                $searched_item_content = $parser->get_get_content($product_url);

                $parts = [
                    'title'     => [
                        'path' => '#wrp-content .product-head-instr > h1',
                    ],
                    'price'     => [
                        'path' => '#wrp-content .card-price .price-new',
                        'options' => [
                            'get_attributes' => 'content'
                        ],
                    ],
                    'parameters' => [
                        'path' => '#wrp-content .parameter-table td',
                        'options' => [
                            'many' => true,
                        ],
                    ],
                    'content_text' => [
                        'path' => '#wrp-content .symptom-content > div',
                        'options' => [
                            'many' => true,
                        ],
                    ]
                ];



                $post_parts = $parser->get_parts($searched_item_content, $parts);

                $parameters = [];
                for($i = 0; $i < count($post_parts['parameters']); $i+=2){
                    $type = strip_tags($post_parts['parameters'][$i]);
                    switch ($type){
                        // text in link
                        case 'Торговое название':
                        case 'Действующие вещества':
                        case 'Код АТС':
                            $value = $parser->get_doms($post_parts['parameters'][$i+1], 'a');
                            break;

                        case 'Категория':
                            $value = $parser->get_doms($post_parts['parameters'][$i+1], 'p');

                            foreach ($value as $key=>$item){
                                $tmp = explode('&gt;', strip_tags($item));
                                foreach($tmp as $tmp_key =>$tmp_value){
                                    $tmp[$tmp_key] = trim($tmp_value);
                                }
                                $value[$key] = $tmp;
                            }
                            break;

                        // list
                        case 'Кому можно':
                            $value = $parser->get_parts($post_parts['parameters'][$i+1], [
                                'items' => [
                                    'path' => 'ul li div',
                                    'options' => [
                                        'many' => true,
                                        'text' => true,
                                    ],
                                ]
                            ]);
                            $allowed = [];
                            for($j=0; $j < count($value['items']) -1; $j+=2){
                                $allowed_to = $value['items'][$j];
                                $allowed_value = $value['items'][$j+1];
                                $allowed[$allowed_to] = $allowed_value;
                            }
                            $value = $allowed;

                            break;

                        // text
                        case 'Форма выпуска':
                        case 'Способ введения':
                        case 'Вид упаковки':
                        case 'Количество в упаковке':
                        case 'Взаимодействие с едой':
                        case 'Чувствительность к свету':
                        case 'Условия отпуска':
                        case 'Срок годности':
                        case 'Температура хранения':
                        case 'Признак отечественный':
                        case 'Производитель':
                        case 'Страна владелец лицензии':
                            $value = strip_tags(trim($post_parts['parameters'][$i+1]));
                            break;

                        // skip
                        case 'Кол-во действ. вещ-ва':
                            break;

                        default:
                            dd('UNKNOWN TYPE! ', $type);
                            break;
                    }
                    $parameters[$type] = [
                        'type' => $type,
                        'value' => $value, // array
                    ];

                }
                //dd($product_url, $parameters);

                // Splitting headers and texts if needed
                //$post_parts['content_text'][0] = trim(str_replace(['<p>', '</p>'], '', $post_parts['content_text'][0]));
                //preg_match_all('/(?:<h2>)(.+)(?:<\/h2>)/', $post_parts['content_text'][0], $headers);
                //preg_match_all('/<\/h2>(.+?)(?:<h2>|$)/s', $post_parts['content_text'][0], $texts);
                //dd($headers[1], $texts[1]);

                // Images
                /*
                $curl_post = [
                    'related' => 1,
                    'id' => 563,
                    'code' => 2598,
                ];
                $curl = curl_init('https://apteka911.com.ua/mmi');
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                //curl_setopt($curl, CURLOPT_HTTPHEADER, '');
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($curl);
                $result = json_decode($result);
                */

                preg_match('/{"cart":.*}\n/', $searched_item_content, $thumbs);
                $thumbs = json_decode($thumbs[0], true);

                $images_urls = [];
                $base_img_url = $thumbs['product']['dataUrl'].'photos';
                //$images_urls[] = $base_img_url.$thumbs['product']['productThumbs']['big']['file'];
                foreach ($thumbs['photos'] as $source_photo){
                    $images_urls[] = $base_img_url.'/'.$source_photo['photoBigFile'];
                }

                /*
                'trade_title' => 'Торговое название',
                'active_substance_id' => '',
                */

                // DB stuff
                try{
                    // Debug
                    /*
                    if($product_url === 'https://apteka911.com.ua/shop/septefril-darnitsa-tabl-0-2mg-20-p35117'){
                        dd($post_parts);
                    }
                    */

                    // Reqs
                    $price = $post_parts['price'][1] ?: $post_parts['price'][2] ?: $post_parts['price'][3] ?: 0;
                    $product_new->title = $post_parts['title'];
                    $product_new->status = 'PUBLISHED';
                    $product_new->price = $price;
                    $product_new->excerpt = excerpt($post_parts['content_text'][0], 120, '...');
                    $product_new->author_id = 1;
                    $product_new->save();


                    // Categories
                    //$categories = [];
                    $is_first_category = true;
                    if(isset($parameters['Категория']['value'])){
                        foreach ($parameters['Категория']['value'] as $parameter_category_chain){
                            // root category
                            $product_category = ProductCategory::where('name', '=', $parameter_category_chain[0])
                                ->where('parent_id', '=', null)
                                ->first();
                            if($product_category === null){
                                $product_category = new ProductCategory();
                                $product_category->name = $parameter_category_chain[0];
                                $product_category->save();
                            }
                            // child category
                            if(isset($parameter_category_chain[1])){
                                //dd($parameter_category_chain[1], $product_category->id);
                                $product_category_child = ProductCategory::where('name', '=', $parameter_category_chain[1])
                                    ->where('parent_id', '=', $product_category->id)
                                    ->first();
                                if($product_category_child === null){
                                    $product_category_child = new ProductCategory();
                                    $product_category_child->name = $parameter_category_chain[1];
                                    $product_category_child->parent_id = $product_category->id;
                                    $product_category_child->save();
                                }
                            }else{
                                $product_category_child = null;
                            }
                            //dd($product_category, $parameter_category_chain, $product_category_child);

                            // assign to product
                            if($is_first_category){
                                $product_new->category_id = $product_category_child->id ?? $product_category->id;
                            }else{
                                $product_new->categories()->attach($product_category_child->id ?? $product_category->id);
                            }


                            $is_first_category = false;

                        }
                    }

                    // Active substances
                    if(isset($parameters['Действующие вещества']['value'])){
                        foreach ($parameters['Действующие вещества']['value'] as $parameter_active_substance){
                            $active_substance = ActiveSubstance::where('title', '=', $parameter_active_substance)
                                ->first();
                            if($active_substance === null){
                                $active_substance = new ActiveSubstance();
                                $active_substance->title = $parameter_active_substance;
                                $active_substance->save();
                            }

                            // assign to product
                            $product_new->active_substances()->attach($active_substance->id);
                        }
                    }

                    // Allowed to
                    if(isset($parameters['Кому можно']['value'])){
                        foreach ($parameters['Кому можно']['value'] as $parameter_allowed_key=>$parameter_allowed_value){
                            switch ($parameter_allowed_key){
                                case 'Взрослым':
                                    $product_new->allowed_adult = $parameter_allowed_value;
                                    break;
                                case 'Детям':
                                    $product_new->allowed_child = $parameter_allowed_value;
                                    break;
                                case 'Беременным':
                                    $product_new->allowed_pregnant = $parameter_allowed_value;
                                    break;
                                case 'Кормящим':
                                    $product_new->allowed_nursing = $parameter_allowed_value;
                                    break;
                                case 'Аллергикам':
                                    $product_new->allowed_allergic = $parameter_allowed_value;
                                    break;
                                case 'Диабетикам':
                                    $product_new->allowed_diabetic = $parameter_allowed_value;
                                    break;
                                case 'Водителям':
                                    $product_new->allowed_driver = $parameter_allowed_value;
                                    break;
                                default:
                                    throw new \Exception('UNKNOWN ALLOWED TYPE! '.$parameter_allowed_key);
                                    break;
                            }
                        }
                    }

                    if(isset($parameters['Торговое название']['value'][0])){
                        $product_new->title_short = $parameters['Торговое название']['value'][0];
                    }
                    if(isset($parameters['Форма выпуска']['value'])){
                        $product_new->release_form = $parameters['Форма выпуска']['value'];
                    }
                    if(isset($parameters['Способ введения']['value'])){
                        $product_new->administration = $parameters['Способ введения']['value'];
                    }
                    if(isset($parameters['Вид упаковки']['value'])){
                        $product_new->packaging_type = $parameters['Вид упаковки']['value'];
                    }
                    if(isset($parameters['Количество в упаковке']['value'])){
                        $product_new->count_in_package = $parameters['Количество в упаковке']['value'];
                    }
                    if(isset($parameters['Взаимодействие с едой']['value'])){
                        $product_new->food_interaction = $parameters['Взаимодействие с едой']['value'];
                    }
                    if(isset($parameters['Чувствительность к свету']['value'])){
                        $product_new->light_sensitivity = $parameters['Чувствительность к свету']['value'];
                    }
                    if(isset($parameters['Условия отпуска']['value'])){
                        $product_new->conditions_of_supply = $parameters['Условия отпуска']['value'];
                    }
                    if(isset($parameters['Срок годности']['value'])){
                        $product_new->expiration_date = $parameters['Срок годности']['value'];
                    }
                    if(isset($parameters['Температура хранения']['value'])){
                        $product_new->storage_temperature = $parameters['Температура хранения']['value'];
                    }
                    if(isset($parameters['Производитель']['value'])){
                        $product_new->manufacturer = $parameters['Производитель']['value'];
                    }
                    if(isset($parameters['Страна владелец лицензии']['value'])){
                        $product_new->country_license_holder = $parameters['Страна владелец лицензии']['value'];
                    }
                    if(isset($parameters['Код АТС']['value'][0])){
                        $product_new->atc_code = $parameters['Код АТС']['value'][0];
                    }

                    $product_new->body = clean_string($post_parts['content_text'][0]);
                    $product_new->parsed_source = $product_url;

                    $product_image = [];
                    $img_counter = 0;
                    foreach ($images_urls as $images_url){
                        $filename = 'prod_'.$product_new->id.'_'.$img_counter;
                        $result = $parser->download_image($images_url, $image_base_path.'/products', $filename);

                        if($result !== false){
                            $product_image[] = 'products/'.$result;
                            if($img_counter === 0){
                                $product_new->image_thumb = 'products/'.$result;
                            }
                        }else{
                            echo "image failed! (".$images_url.")".PHP_EOL;
                        }
                        $product_new->image = json_encode($product_image, JSON_UNESCAPED_SLASHES);

                        $img_counter++;
                    }


                    $product_new->save();
                }catch (\Exception $exception){
                    $product_new->delete();
                    dd($exception->getMessage());
                }




                $item_count++;
            }
            echo "PAGE FINISHED".PHP_EOL;

        }

        echo 'FINISHED. $item_count = '.$item_count.PHP_EOL;
        echo "</pre>";
    }

    public function parse_asna(){
        echo '<pre>'.PHP_EOL;

        $image_base_path = public_path('uploads');
        $parser = new HtmlParser();
        $base_url = 'https://www.asna.ru/';

        $item_count = 0;
        $item_limit = 100;
        $page_limit = -1;    // Max ending page
        $lvl2_max = -1;

        $skip_lvl2 = 0; // Skip
        $skip_lvl3 = 0;
        $skip_pages = 0;
        $skip_until = config('auto_parser.cat_level_3_name', null);
        $is_skip_until = true;

        //$cat_index = 19;

        $tmp_distinct_who_allowed = [];

        /*
        $searched_items = $parser->get_urls($html, 'body .block-groups-list > ul > li > a');
            //$parser->get_doms($html, 'body .block-groups-list > ul > li > a');
        dd($searched_items);
        */






        $cats_urls = [
            //'Лекарства' => 'https://www.asna.ru/catalog/lekarstva/', // READY
            //'БАД' => 'https://www.asna.ru/catalog/bad/', // READY
            'Гомеопатия' => 'https://www.asna.ru/catalog/gomeopatiya/', // Not finished, has 1 level
            //'Медицинские изделия' => 'https://www.asna.ru/catalog/izdeliya_medicinskogo_naznacheniya/', // READY
            //'Ортопедия' => 'https://www.asna.ru/catalog/ortopedicheskie_tovary/', // READY
            //'Оптика' => 'https://www.asna.ru/catalog/tovary_optiki/', // READY
            //'Косметика' => 'https://www.asna.ru/catalog/kosmetika/', // READY
            //'Средства гигиены' => 'https://www.asna.ru/catalog/sredstva_lichnoj_gigieny/', // READY
            //'Товары для дома' => 'https://www.asna.ru/catalog/tovary_dlya_doma/', // READY
            //'Мама и малыш' => 'https://www.asna.ru/catalog/tovary_dlya_mam_i_malyshej/', // READY
            //'Питание' => 'https://www.asna.ru/catalog/produkty_pitaniya/', // READY
           //'Травы/Чаи' => 'https://www.asna.ru/catalog/travy_chai/', // READY
          //'Массмаркет' => 'https://www.asna.ru/catalog/massmarket/', // READY
          //'Изделия медицинского назначения' => 'https://www.asna.ru/catalog/izdeliya_meditsinskogo_naznacheniya/', // READY
        ];

        $config = [
            'categories' => [
                'path' => '.menu-section li a',
            ],
            'items' => [
                'path' => '.section-main__content__products__list .product__box .product__information a',
            ],
            'pagination' => [
                'path' => '.pagination__pages'
            ],
        ];

        foreach ($cats_urls as $cat_level_1_name => $cat_level_1_url){
            $config['url'] = $cat_level_1_url;
            $result1 = $this->get_archive($config, $base_url);
            if($result1['categories'] !== null){
                $lvl2_count = 1;
                foreach ($result1['categories'] as ['text' => $cat_level_2_name, 'url' => $cat_level_2_url]){
                    if($lvl2_count > $lvl2_max && $lvl2_max !== -1){
                        break;
                    }
                    if($skip_lvl2 > 0){
                        $skip_lvl2--;
                        continue;
                    }
                    echo "CATEGORY LVL2 NAME ".$cat_level_2_name.PHP_EOL;
                    $config['url'] = $cat_level_2_url;
                    $result2 = $this->get_archive($config, $base_url);


                    if($result2['categories'] === null){
                        $max_level = 2;
                    }else{
                        //$result2 = $result1;
                        //$result1['categories'] = [];
                        $max_level = 3;
                    }
                    //dd($result1['categories'], $result2['categories']);

                    if($max_level === 3){
                        foreach ($result2['categories'] as ['text' => $cat_level_3_name, 'url' => $cat_level_3_url]){
                            // skip until cat name encountered
                            if($skip_until !== null && $is_skip_until === true){
                                if($skip_until === $cat_level_3_name){
                                    $is_skip_until = false;
                                }else{
                                    continue;
                                }
                            }
                            // skip number
                            if($skip_lvl3 > 0){
                                $skip_lvl3--;
                                continue;
                            }
                            echo "CATEGORY LVL3 NAME ".$cat_level_3_name.PHP_EOL;
                            $config['url'] = $cat_level_3_url;
                            $result3 = $this->get_archive($config, $base_url);
                            //$pagination = $this->get_pagination($result3['html'], $config, $base_url);

                            $first_item_url = '';
                            $is_page_correct = true;
                            $page_counter = 1;
                            while ($is_page_correct && ($page_counter <= $page_limit || $page_limit === -1)){
                                if($skip_pages > 0){
                                    $skip_pages--;
                                    continue;
                                }
                                $archive_page_url = $cat_level_3_url.'?PAGEN_1='.$page_counter;

                                //$searched_items = $this->get_items($archive_page_url, $config, $base_url);
                                $searched_items_html = $parser->get_get_content($archive_page_url);
                                $searched_items = $parser->get_doms($searched_items_html, '.section-main__content__products__list .product__box .product__information');

                                foreach ($searched_items as $searched_item){
                                    $searched_item = $parser->get_parts($searched_item, [
                                        'data' => [
                                            'path' => 'a'
                                        ],
                                    ], $base_url);
                                    // Page count overkill
                                    if($first_item_url === $searched_item['data'][0]['url']){
                                        $is_page_correct = false;
                                        echo "PAGE OVERKILL ".$first_item_url.PHP_EOL;
                                        break 2;
                                    }
                                    if($first_item_url === ''){
                                        $first_item_url = $searched_item['data'][0]['url'];
                                    }

                                    if($item_count >= $item_limit and $item_limit !== -1){
                                        break 5;
                                    }

                                    // Link to post
                                    $product_url = $searched_item['data'][0]['url'];

                                    $product_new = Product::where('parsed_source', '=', $product_url)
                                        ->first();
                                    if($product_new !== null){
                                        continue;
                                    }
                                    $product_new = new Product();
                                    echo $product_url.PHP_EOL;

                                    $searched_item_content = $parser->get_get_content($product_url);
                                    //dd($searched_item_content);
                                    $parts = [
                                        'title'     => [
                                            'path' => '.product-title > h1',
                                        ],
                                        'price'     => [
                                            'path' => '.product__price span',
                                            'options' => [
                                                //'get_attributes' => 'content'
                                            ],
                                        ],
                                        'parameters' => [
                                            'path' => '.adtcp .infos li span',
                                            'options' => [
                                                'many' => true,
                                            ],
                                        ],
                                        'content_text' => [
                                            'path' => '.product-information__info__content',
                                            'options' => [
                                                'many' => true,
                                            ],
                                        ],
                                        'images' => [
                                            'path' => 'img.js-main-item-photo',
                                            'options' => [
                                                //'get_attributes' => 'style'
                                            ],
                                        ]
                                    ];
                                    $post_parts = $parser->get_parts($searched_item_content, $parts, $base_url);
                                    if($post_parts['title'] === null){
                                        echo "TITLE IS NULL ".$product_url.PHP_EOL;
                                        continue;
                                    }
                                    //dd($post_parts);

                                    $parameters = [];
                                    if(!is_array($post_parts['parameters'])){
                                        echo ">>WARNING<< No parameters".PHP_EOL;
                                        $post_parts['parameters'] = [];
                                    }
                                    for($i = 0; $i < count($post_parts['parameters']); $i+=2){
                                        $type = trim(strip_tags($post_parts['parameters'][$i]), ':');
                                        switch ($type){
                                            // text in link
                                            case 'Действующее вещество':
                                                $value = $parser->get_doms($post_parts['parameters'][$i+1], 'a');
                                                $value = explode('+', $value[0]);
                                                break;

                                            /*
                                            case 'Код АТС':
                                                $value = $parser->get_doms($post_parts['parameters'][$i+1], 'a');
                                                break;
                                            */

                                            // list
                                            /*
                                            case 'Кому можно':
                                                $value = $parser->get_parts($post_parts['parameters'][$i+1], [
                                                    'items' => [
                                                        'path' => 'ul li div',
                                                        'options' => [
                                                            'many' => true,
                                                            'text' => true,
                                                        ],
                                                    ]
                                                ]);
                                                $allowed = [];
                                                for($j=0; $j < count($value['items']) -1; $j+=2){
                                                    $allowed_to = $value['items'][$j];
                                                    $allowed_value = $value['items'][$j+1];
                                                    $allowed[$allowed_to] = $allowed_value;
                                                }
                                                $value = $allowed;

                                                break;
                                            */

                                            // text
                                            case 'Фасовка':
                                            case 'Форма выпуска':
                                            case 'Упаковка':
                                            case 'Производитель':
                                            case 'Завод-производитель':
                                            case 'Дозировка':
                                                $value = strip_tags(trim($post_parts['parameters'][$i+1]));
                                                break;

                                            // skip

                                            default:
                                                dd('UNKNOWN TYPE! ', $type);
                                                break;
                                        }
                                        $parameters[$type] = [
                                            'type' => $type,
                                            'value' => $value, // array
                                        ];

                                    }
                                    //dd($product_url, $post_parts, $parameters);


                                    $images_urls = [
                                        $post_parts['images']
                                    ];

                                    try{
                                        // Debug
                                        /*
                                        if($product_url === 'https://apteka911.com.ua/shop/septefril-darnitsa-tabl-0-2mg-20-p35117'){
                                            dd($post_parts);
                                        }
                                        */

                                        // Reqs
                                        $price = str_replace(' ', '', $post_parts['price']);
                                        if($price === ''){
                                            echo "PRICE EMPTY".PHP_EOL;
                                            $price = 0;
                                        }
                                        if($price === 'Ценаот'){
                                            echo "PRICE IS STRANGE ".$price.PHP_EOL;
                                            $price = 0;
                                        }
                                        $product_new->title = $post_parts['title'];
                                        $product_new->status = 'PUBLISHED';
                                        $product_new->price = $price;
                                        $body = clean_string($post_parts['content_text'][2] ?? '');
                                        $product_new->body = $body;
                                        $product_new->excerpt = excerpt($body, 120, '...');
                                        $product_new->author_id = 1;
                                        $product_new->save();


                                        // Categories
                                        // Use 2-dimensional array
                                        if($max_level === 2){
                                            $categories = [
                                                [
                                                    $cat_level_1_name,
                                                    $cat_level_2_name,
                                                ],
                                            ];
                                        }elseif($max_level === 3){
                                            $categories = [
                                                [
                                                    $cat_level_1_name,
                                                    $cat_level_2_name,
                                                    $cat_level_3_name,
                                                ],
                                            ];
                                        }

                                        $is_first_category = true;
                                        if(isset($categories)){
                                            foreach ($categories as $category_chain){
                                                // create categories
                                                $product_category_parent = null;
                                                $product_category_child = null;
                                                foreach ($category_chain as $category_chain_item){
                                                    $product_category_child = ProductCategory::where('name', '=', $category_chain_item)
                                                        ->where('parent_id', '=', $product_category_parent->id ?? null)
                                                        ->first();
                                                    if($product_category_child === null){
                                                        $product_category_child = new ProductCategory();
                                                        $product_category_child->name = $category_chain_item;
                                                        $product_category_child->parent_id = $product_category_parent->id ?? null;
                                                        $product_category_child->save();
                                                    }
                                                    $product_category_parent = $product_category_child;
                                                }

                                                // assign to product
                                                if($is_first_category){
                                                    $product_new->category_id = $product_category_child->id;
                                                }else{
                                                    $product_new->categories()->attach($product_category_child->id);
                                                }

                                                $is_first_category = false;
                                            }
                                        }

                                        // Active substances
                                        if(isset($parameters['Действующее вещество']['value'])){
                                            foreach ($parameters['Действующее вещество']['value'] as $parameter_active_substance){
                                                $parameter_active_substance = trim($parameter_active_substance);
                                                $active_substance = ActiveSubstance::where('title', '=', $parameter_active_substance)
                                                    ->first();
                                                if($active_substance === null){
                                                    $active_substance = new ActiveSubstance();
                                                    $active_substance->title = $parameter_active_substance;
                                                    $active_substance->save();
                                                }

                                                // assign to product
                                                $product_new->active_substances()->attach($active_substance->id);
                                            }
                                        }

                                        // Allowed to
                                        /*
                                        if(isset($parameters['Кому можно']['value'])){
                                            foreach ($parameters['Кому можно']['value'] as $parameter_allowed_key=>$parameter_allowed_value){
                                                switch ($parameter_allowed_key){
                                                    case 'Взрослым':
                                                        $product_new->allowed_adult = $parameter_allowed_value;
                                                        break;
                                                    case 'Детям':
                                                        $product_new->allowed_child = $parameter_allowed_value;
                                                        break;
                                                    case 'Беременным':
                                                        $product_new->allowed_pregnant = $parameter_allowed_value;
                                                        break;
                                                    case 'Кормящим':
                                                        $product_new->allowed_nursing = $parameter_allowed_value;
                                                        break;
                                                    case 'Аллергикам':
                                                        $product_new->allowed_allergic = $parameter_allowed_value;
                                                        break;
                                                    case 'Диабетикам':
                                                        $product_new->allowed_diabetic = $parameter_allowed_value;
                                                        break;
                                                    case 'Водителям':
                                                        $product_new->allowed_driver = $parameter_allowed_value;
                                                        break;
                                                    default:
                                                        throw new \Exception('UNKNOWN ALLOWED TYPE! '.$parameter_allowed_key);
                                                        break;
                                                }
                                            }
                                        }
                                        */

                                        // Brand
                                        $title_short = $searched_item['data'][1]['text'] ?? null;
                                        $product_new->title_short = $title_short;

                                        if(isset($parameters['Фасовка']['value'])){
                                            $product_new->packing = $parameters['Фасовка']['value'];
                                        }
                                        if(isset($parameters['Форма выпуска']['value'])){
                                            $product_new->release_form = $parameters['Форма выпуска']['value'];
                                        }
                                        if(isset($parameters['Упаковка']['value'])){
                                            $product_new->packaging_type = $parameters['Упаковка']['value'];
                                        }




                                        /*if(isset($parameters['Способ введения']['value'])){
                                            $product_new->administration = $parameters['Способ введения']['value'];
                                        }*/
                                        /*if(isset($parameters['Количество в упаковке']['value'])){
                                            $product_new->count_in_package = $parameters['Количество в упаковке']['value'];
                                        }*/
                                        /*if(isset($parameters['Взаимодействие с едой']['value'])){
                                            $product_new->food_interaction = $parameters['Взаимодействие с едой']['value'];
                                        }*/
                                        /*if(isset($parameters['Чувствительность к свету']['value'])){
                                            $product_new->light_sensitivity = $parameters['Чувствительность к свету']['value'];
                                        }*/
                                        /*if(isset($parameters['Условия отпуска']['value'])){
                                            $product_new->conditions_of_supply = $parameters['Условия отпуска']['value'];
                                        }*/
                                        /*if(isset($parameters['Срок годности']['value'])){
                                            $product_new->expiration_date = $parameters['Срок годности']['value'];
                                        }*/
                                        /*if(isset($parameters['Температура хранения']['value'])){
                                            $product_new->storage_temperature = $parameters['Температура хранения']['value'];
                                        }*/
                                        /*if(isset($parameters['Производитель']['value'])){
                                            $product_new->manufacturer = $parameters['Производитель']['value'];
                                        }*/
                                        /*if(isset($parameters['Страна владелец лицензии']['value'])){
                                            $product_new->country_license_holder = $parameters['Страна владелец лицензии']['value'];
                                        }*/
                                        /*if(isset($parameters['Код АТС']['value'][0])){
                                            $product_new->atc_code = $parameters['Код АТС']['value'][0];
                                        }*/

                                        $product_new->parsed_source = $product_url;

                                        $product_image = [];
                                        $img_counter = 0;
                                        foreach ($images_urls as $images_url){
                                            $folder_thousand = intdiv((int)$product_new->id, 1000) * 1000;
                                            $folder_thousand = $folder_thousand.'-'.($folder_thousand+999);
                                            $folder_hundred = intdiv((int)$product_new->id, 100) * 100;
                                            $folder_hundred = $folder_hundred.'-'.($folder_hundred+99);
                                            $filepath = 'products/'.$folder_thousand.'/'.$folder_hundred.'/'.'prod_'.$product_new->id;
                                            $filename = 'prod_img_'.$product_new->id.'_'.$img_counter;
                                            $result = $parser->download_image($images_url, $image_base_path.'/'.$filepath, $filename);

                                            if($result !== false){
                                                $product_image[] = $filepath.'/'.$result;
                                                if($img_counter === 0){
                                                    $product_new->image_thumb = $filepath.'/'.$result;
                                                }
                                            }else{
                                                echo "image failed! (".$images_url.")".PHP_EOL;
                                            }
                                            $product_new->image = json_encode($product_image, JSON_UNESCAPED_SLASHES);

                                            $img_counter++;
                                        }
                                        $product_new->save();

                                    }catch (\Exception $exception){
                                        $product_new->delete();
                                        dd($exception->getMessage());
                                    }

                                    $item_count++;
                                }

                                echo "PAGE FINISHED ".$page_counter.PHP_EOL;
                                $page_counter++;
                            }

                            save_config(['cat_level_2_name' => $cat_level_2_name, 'cat_level_3_name' => $cat_level_3_name], 'auto_parser');
                        }
                        $skip_lvl3 = 0;
                        $lvl2_count++;
                        echo "CATEGORY LVL2 FINISHED ".$cat_level_2_name.PHP_EOL;
                    }elseif($max_level === 2){
                        $config['url'] = $cat_level_2_url;
                        $result3 = $this->get_archive($config, $base_url);
                        //$pagination = $this->get_pagination($result3['html'], $config, $base_url);

                        $first_item_url = '';
                        $is_page_correct = true;
                        $page_counter = 1;
                        while ($is_page_correct && ($page_counter <= $page_limit || $page_limit === -1)){
                                if($skip_pages > 0){
                                    $skip_pages--;
                                    continue;
                                }
                                $archive_page_url = $cat_level_2_url.'?PAGEN_1='.$page_counter;

                                //$searched_items = $this->get_items($archive_page_url, $config, $base_url);
                                $searched_items_html = $parser->get_get_content($archive_page_url);
                                $searched_items = $parser->get_doms($searched_items_html, '.section-main__content__products__list .product__box .product__information');

                                foreach ($searched_items as $searched_item){
                                    $searched_item = $parser->get_parts($searched_item, [
                                        'data' => [
                                            'path' => 'a'
                                        ],
                                    ], $base_url);
                                    // Page count overkill
                                    if($first_item_url === $searched_item['data'][0]['url']){
                                        $is_page_correct = false;
                                        echo "PAGE OVERKILL ".$first_item_url.PHP_EOL;
                                        break 1;
                                    }
                                    if($first_item_url === ''){
                                        $first_item_url = $searched_item['data'][0]['url'];
                                    }

                                    if($item_count >= $item_limit and $item_limit !== -1){
                                        break 4;
                                    }

                                    // Link to post
                                    $product_url = $searched_item['data'][0]['url'];

                                    $product_new = Product::where('parsed_source', '=', $product_url)
                                        ->first();
                                    if($product_new !== null){
                                        continue;
                                    }
                                    $product_new = new Product();
                                    echo $product_url.PHP_EOL;

                                    $searched_item_content = $parser->get_get_content($product_url);
                                    //dd($searched_item_content);
                                    $parts = [
                                        'title'     => [
                                            'path' => '.product-title > h1',
                                        ],
                                        'price'     => [
                                            'path' => '.product__price span',
                                            'options' => [
                                                //'get_attributes' => 'content'
                                            ],
                                        ],
                                        'parameters' => [
                                            'path' => '.adtcp .infos li span',
                                            'options' => [
                                                'many' => true,
                                            ],
                                        ],
                                        'content_text' => [
                                            'path' => '.product-information__info__content',
                                            'options' => [
                                                'many' => true,
                                            ],
                                        ],
                                        'images' => [
                                            'path' => 'img.js-main-item-photo',
                                            'options' => [
                                                //'get_attributes' => 'style'
                                            ],
                                        ]
                                    ];
                                    $post_parts = $parser->get_parts($searched_item_content, $parts, $base_url);
                                    //dd($post_parts);

                                    $parameters = [];
                                    if(!is_array($post_parts['parameters'])){
                                        echo ">>WARNING<< No parameters".PHP_EOL;
                                        $post_parts['parameters'] = [];
                                    }
                                    for($i = 0; $i < count($post_parts['parameters']); $i+=2){
                                        $type = trim(strip_tags($post_parts['parameters'][$i]), ':');
                                        switch ($type){
                                            // text in link
                                            case 'Действующее вещество':
                                                $value = $parser->get_doms($post_parts['parameters'][$i+1], 'a');
                                                $value = explode('+', $value[0]);
                                                break;

                                            /*
                                            case 'Код АТС':
                                                $value = $parser->get_doms($post_parts['parameters'][$i+1], 'a');
                                                break;
                                            */

                                            // list
                                            /*
                                            case 'Кому можно':
                                                $value = $parser->get_parts($post_parts['parameters'][$i+1], [
                                                    'items' => [
                                                        'path' => 'ul li div',
                                                        'options' => [
                                                            'many' => true,
                                                            'text' => true,
                                                        ],
                                                    ]
                                                ]);
                                                $allowed = [];
                                                for($j=0; $j < count($value['items']) -1; $j+=2){
                                                    $allowed_to = $value['items'][$j];
                                                    $allowed_value = $value['items'][$j+1];
                                                    $allowed[$allowed_to] = $allowed_value;
                                                }
                                                $value = $allowed;

                                                break;
                                            */

                                            // text
                                            case 'Фасовка':
                                            case 'Форма выпуска':
                                            case 'Упаковка':
                                            case 'Производитель':
                                            case 'Завод-производитель':
                                            case 'Дозировка':
                                                $value = strip_tags(trim($post_parts['parameters'][$i+1]));
                                                break;

                                            // skip

                                            default:
                                                dd('UNKNOWN TYPE! ', $type);
                                                break;
                                        }
                                        $parameters[$type] = [
                                            'type' => $type,
                                            'value' => $value, // array
                                        ];

                                    }
                                    //dd($product_url, $post_parts, $parameters);


                                    $images_urls = [
                                        $post_parts['images']
                                    ];

                                    try{
                                        // Debug
                                        /*
                                        if($product_url === 'https://apteka911.com.ua/shop/septefril-darnitsa-tabl-0-2mg-20-p35117'){
                                            dd($post_parts);
                                        }
                                        */

                                        // Reqs
                                        $price = str_replace(' ', '', $post_parts['price']);
                                        if($price === ''){
                                            echo "PRICE EMPTY".PHP_EOL;
                                            $price = 0;
                                        }
                                        if($price === 'Ценаот'){
                                            echo "PRICE IS STRANGE ".$price.PHP_EOL;
                                            $price = 0;
                                        }
                                        $product_new->title = $post_parts['title'];
                                        $product_new->status = 'PUBLISHED';
                                        $product_new->price = $price;
                                        $body = clean_string($post_parts['content_text'][2] ?? '');
                                        $product_new->body = $body;
                                        $product_new->excerpt = excerpt($body, 120, '...');
                                        $product_new->author_id = 1;
                                        $product_new->save();


                                        // Categories
                                        // Use 2-dimensional array
                                        $categories = [
                                            [
                                                $cat_level_1_name,
                                                $cat_level_2_name,
                                            ],
                                        ];


                                        $is_first_category = true;
                                        if(isset($categories)){
                                            foreach ($categories as $category_chain){
                                                // create categories
                                                $product_category_parent = null;
                                                $product_category_child = null;
                                                foreach ($category_chain as $category_chain_item){
                                                    $product_category_child = ProductCategory::where('name', '=', $category_chain_item)
                                                        ->where('parent_id', '=', $product_category_parent->id ?? null)
                                                        ->first();
                                                    if($product_category_child === null){
                                                        $product_category_child = new ProductCategory();
                                                        $product_category_child->name = $category_chain_item;
                                                        $product_category_child->parent_id = $product_category_parent->id ?? null;
                                                        $product_category_child->save();
                                                    }
                                                    $product_category_parent = $product_category_child;
                                                }

                                                // assign to product
                                                if($is_first_category){
                                                    $product_new->category_id = $product_category_child->id;
                                                }else{
                                                    $product_new->categories()->attach($product_category_child->id);
                                                }

                                                $is_first_category = false;
                                            }
                                        }

                                        // Active substances
                                        if(isset($parameters['Действующее вещество']['value'])){
                                            foreach ($parameters['Действующее вещество']['value'] as $parameter_active_substance){
                                                $parameter_active_substance = trim($parameter_active_substance);
                                                $active_substance = ActiveSubstance::where('title', '=', $parameter_active_substance)
                                                    ->first();
                                                if($active_substance === null){
                                                    $active_substance = new ActiveSubstance();
                                                    $active_substance->title = $parameter_active_substance;
                                                    $active_substance->save();
                                                }

                                                // assign to product
                                                $product_new->active_substances()->attach($active_substance->id);
                                            }
                                        }

                                        // Allowed to
                                        /*
                                        if(isset($parameters['Кому можно']['value'])){
                                            foreach ($parameters['Кому можно']['value'] as $parameter_allowed_key=>$parameter_allowed_value){
                                                switch ($parameter_allowed_key){
                                                    case 'Взрослым':
                                                        $product_new->allowed_adult = $parameter_allowed_value;
                                                        break;
                                                    case 'Детям':
                                                        $product_new->allowed_child = $parameter_allowed_value;
                                                        break;
                                                    case 'Беременным':
                                                        $product_new->allowed_pregnant = $parameter_allowed_value;
                                                        break;
                                                    case 'Кормящим':
                                                        $product_new->allowed_nursing = $parameter_allowed_value;
                                                        break;
                                                    case 'Аллергикам':
                                                        $product_new->allowed_allergic = $parameter_allowed_value;
                                                        break;
                                                    case 'Диабетикам':
                                                        $product_new->allowed_diabetic = $parameter_allowed_value;
                                                        break;
                                                    case 'Водителям':
                                                        $product_new->allowed_driver = $parameter_allowed_value;
                                                        break;
                                                    default:
                                                        throw new \Exception('UNKNOWN ALLOWED TYPE! '.$parameter_allowed_key);
                                                        break;
                                                }
                                            }
                                        }
                                        */

                                        // Brand
                                        $title_short = $searched_item['data'][1]['text'] ?? null;
                                        $product_new->title_short = $title_short;

                                        if(isset($parameters['Фасовка']['value'])){
                                            $product_new->packing = $parameters['Фасовка']['value'];
                                        }
                                        if(isset($parameters['Форма выпуска']['value'])){
                                            $product_new->release_form = $parameters['Форма выпуска']['value'];
                                        }
                                        if(isset($parameters['Упаковка']['value'])){
                                            $product_new->packaging_type = $parameters['Упаковка']['value'];
                                        }




                                        /*if(isset($parameters['Способ введения']['value'])){
                                            $product_new->administration = $parameters['Способ введения']['value'];
                                        }*/
                                        /*if(isset($parameters['Количество в упаковке']['value'])){
                                            $product_new->count_in_package = $parameters['Количество в упаковке']['value'];
                                        }*/
                                        /*if(isset($parameters['Взаимодействие с едой']['value'])){
                                            $product_new->food_interaction = $parameters['Взаимодействие с едой']['value'];
                                        }*/
                                        /*if(isset($parameters['Чувствительность к свету']['value'])){
                                            $product_new->light_sensitivity = $parameters['Чувствительность к свету']['value'];
                                        }*/
                                        /*if(isset($parameters['Условия отпуска']['value'])){
                                            $product_new->conditions_of_supply = $parameters['Условия отпуска']['value'];
                                        }*/
                                        /*if(isset($parameters['Срок годности']['value'])){
                                            $product_new->expiration_date = $parameters['Срок годности']['value'];
                                        }*/
                                        /*if(isset($parameters['Температура хранения']['value'])){
                                            $product_new->storage_temperature = $parameters['Температура хранения']['value'];
                                        }*/
                                        /*if(isset($parameters['Производитель']['value'])){
                                            $product_new->manufacturer = $parameters['Производитель']['value'];
                                        }*/
                                        /*if(isset($parameters['Страна владелец лицензии']['value'])){
                                            $product_new->country_license_holder = $parameters['Страна владелец лицензии']['value'];
                                        }*/
                                        /*if(isset($parameters['Код АТС']['value'][0])){
                                            $product_new->atc_code = $parameters['Код АТС']['value'][0];
                                        }*/

                                        $product_new->parsed_source = $product_url;

                                        $product_image = [];
                                        $img_counter = 0;
                                        foreach ($images_urls as $images_url){
                                            $folder_thousand = intdiv((int)$product_new->id, 1000) * 1000;
                                            $folder_thousand = $folder_thousand.'-'.($folder_thousand+999);
                                            $folder_hundred = intdiv((int)$product_new->id, 100) * 100;
                                            $folder_hundred = $folder_hundred.'-'.($folder_hundred+99);
                                            $filepath = 'products/'.$folder_thousand.'/'.$folder_hundred.'/'.'prod_'.$product_new->id;
                                            $filename = 'prod_img_'.$product_new->id.'_'.$img_counter;
                                            $result = $parser->download_image($images_url, $image_base_path.'/'.$filepath, $filename);

                                            if($result !== false){
                                                $product_image[] = $filepath.'/'.$result;
                                                if($img_counter === 0){
                                                    $product_new->image_thumb = $filepath.'/'.$result;
                                                }
                                            }else{
                                                echo "image failed! (".$images_url.")".PHP_EOL;
                                            }
                                            $product_new->image = json_encode($product_image, JSON_UNESCAPED_SLASHES);

                                            $img_counter++;
                                        }
                                        $product_new->save();

                                    }catch (\Exception $exception){
                                        $product_new->delete();
                                        dd($exception->getMessage());
                                    }

                                    $item_count++;
                                }

                                echo "PAGE FINISHED ".$page_counter.PHP_EOL;
                                $page_counter++;
                        }

                        $skip_lvl3 = 0;
                        $lvl2_count++;
                        echo "CATEGORY LVL2 FINISHED ".$cat_level_2_name.PHP_EOL;
                    }else{

                    }

                }
                $skip_lvl2 = 0;
            }else{
                $config['url'] = $cat_level_1_url;
                $result3 = $this->get_archive($config, $base_url);
                //$pagination = $this->get_pagination($result3['html'], $config, $base_url);

                $first_item_url = '';
                $is_page_correct = true;
                $page_counter = 1;
                while ($is_page_correct && ($page_counter <= $page_limit || $page_limit === -1)){
                    if($skip_pages > 0){
                        $skip_pages--;
                        continue;
                    }
                    $archive_page_url = $cat_level_1_url.'?PAGEN_1='.$page_counter;

                    //$searched_items = $this->get_items($archive_page_url, $config, $base_url);
                    $searched_items_html = $parser->get_get_content($archive_page_url);
                    $searched_items = $parser->get_doms($searched_items_html, '.section-main__content__products__list .product__box .product__information');

                    foreach ($searched_items as $searched_item){
                        $searched_item = $parser->get_parts($searched_item, [
                            'data' => [
                                'path' => 'a'
                            ],
                        ], $base_url);
                        // Page count overkill
                        if($first_item_url === $searched_item['data'][0]['url']){
                            $is_page_correct = false;
                            echo "PAGE OVERKILL ".$first_item_url.PHP_EOL;
                            break 1;
                        }
                        if($first_item_url === ''){
                            $first_item_url = $searched_item['data'][0]['url'];
                        }

                        if($item_count >= $item_limit and $item_limit !== -1){
                            break 3;
                        }

                        // Link to post
                        $product_url = $searched_item['data'][0]['url'];

                        $product_new = Product::where('parsed_source', '=', $product_url)
                            ->first();
                        if($product_new !== null){
                            continue;
                        }
                        $product_new = new Product();
                        echo $product_url.PHP_EOL;

                        $searched_item_content = $parser->get_get_content($product_url);
                        //dd($searched_item_content);
                        $parts = [
                            'title'     => [
                                'path' => '.product-title > h1',
                            ],
                            'price'     => [
                                'path' => '.product__price span',
                                'options' => [
                                    //'get_attributes' => 'content'
                                ],
                            ],
                            'parameters' => [
                                'path' => '.adtcp .infos li span',
                                'options' => [
                                    'many' => true,
                                ],
                            ],
                            'content_text' => [
                                'path' => '.product-information__info__content',
                                'options' => [
                                    'many' => true,
                                ],
                            ],
                            'images' => [
                                'path' => 'img.js-main-item-photo',
                                'options' => [
                                    //'get_attributes' => 'style'
                                ],
                            ]
                        ];
                        $post_parts = $parser->get_parts($searched_item_content, $parts, $base_url);
                        //dd($post_parts);

                        $parameters = [];
                        if(!is_array($post_parts['parameters'])){
                            echo ">>WARNING<< No parameters".PHP_EOL;
                            $post_parts['parameters'] = [];
                        }
                        for($i = 0; $i < count($post_parts['parameters']); $i+=2){
                            $type = trim(strip_tags($post_parts['parameters'][$i]), ':');
                            switch ($type){
                                // text in link
                                case 'Действующее вещество':
                                    $value = $parser->get_doms($post_parts['parameters'][$i+1], 'a');
                                    $value = explode('+', $value[0]);
                                    break;

                                /*
                                case 'Код АТС':
                                    $value = $parser->get_doms($post_parts['parameters'][$i+1], 'a');
                                    break;
                                */

                                // list
                                /*
                                case 'Кому можно':
                                    $value = $parser->get_parts($post_parts['parameters'][$i+1], [
                                        'items' => [
                                            'path' => 'ul li div',
                                            'options' => [
                                                'many' => true,
                                                'text' => true,
                                            ],
                                        ]
                                    ]);
                                    $allowed = [];
                                    for($j=0; $j < count($value['items']) -1; $j+=2){
                                        $allowed_to = $value['items'][$j];
                                        $allowed_value = $value['items'][$j+1];
                                        $allowed[$allowed_to] = $allowed_value;
                                    }
                                    $value = $allowed;

                                    break;
                                */

                                // text
                                case 'Фасовка':
                                case 'Форма выпуска':
                                case 'Упаковка':
                                case 'Производитель':
                                case 'Завод-производитель':
                                case 'Дозировка':
                                    $value = strip_tags(trim($post_parts['parameters'][$i+1]));
                                    break;

                                // skip

                                default:
                                    dd('UNKNOWN TYPE! ', $type);
                                    break;
                            }
                            $parameters[$type] = [
                                'type' => $type,
                                'value' => $value, // array
                            ];

                        }
                        //dd($product_url, $post_parts, $parameters);


                        $images_urls = [
                            $post_parts['images']
                        ];

                        try{
                            // Debug
                            /*
                            if($product_url === 'https://apteka911.com.ua/shop/septefril-darnitsa-tabl-0-2mg-20-p35117'){
                                dd($post_parts);
                            }
                            */

                            // Reqs
                            $price = str_replace(' ', '', $post_parts['price']);
                            if($price === ''){
                                echo "PRICE EMPTY".PHP_EOL;
                                $price = 0;
                            }
                            if($price === 'Ценаот'){
                                echo "PRICE IS STRANGE ".$price.PHP_EOL;
                                $price = 0;
                            }
                            $product_new->title = $post_parts['title'];
                            $product_new->status = 'PUBLISHED';
                            $product_new->price = $price;
                            $body = clean_string($post_parts['content_text'][2] ?? '');
                            $product_new->body = $body;
                            $product_new->excerpt = excerpt($body, 120, '...');
                            $product_new->author_id = 1;
                            $product_new->save();


                            // Categories
                            // Use 2-dimensional array
                            $categories = [
                                [
                                    $cat_level_1_name,
                                ],
                            ];


                            $is_first_category = true;
                            if(isset($categories)){
                                foreach ($categories as $category_chain){
                                    // create categories
                                    $product_category_parent = null;
                                    $product_category_child = null;
                                    foreach ($category_chain as $category_chain_item){
                                        $product_category_child = ProductCategory::where('name', '=', $category_chain_item)
                                            ->where('parent_id', '=', $product_category_parent->id ?? null)
                                            ->first();
                                        if($product_category_child === null){
                                            $product_category_child = new ProductCategory();
                                            $product_category_child->name = $category_chain_item;
                                            $product_category_child->parent_id = $product_category_parent->id ?? null;
                                            $product_category_child->save();
                                        }
                                        $product_category_parent = $product_category_child;
                                    }

                                    // assign to product
                                    if($is_first_category){
                                        $product_new->category_id = $product_category_child->id;
                                    }else{
                                        $product_new->categories()->attach($product_category_child->id);
                                    }

                                    $is_first_category = false;
                                }
                            }

                            // Active substances
                            if(isset($parameters['Действующее вещество']['value'])){
                                foreach ($parameters['Действующее вещество']['value'] as $parameter_active_substance){
                                    $parameter_active_substance = trim($parameter_active_substance);
                                    $active_substance = ActiveSubstance::where('title', '=', $parameter_active_substance)
                                        ->first();
                                    if($active_substance === null){
                                        $active_substance = new ActiveSubstance();
                                        $active_substance->title = $parameter_active_substance;
                                        $active_substance->save();
                                    }

                                    // assign to product
                                    $product_new->active_substances()->attach($active_substance->id);
                                }
                            }

                            // Allowed to
                            /*
                            if(isset($parameters['Кому можно']['value'])){
                                foreach ($parameters['Кому можно']['value'] as $parameter_allowed_key=>$parameter_allowed_value){
                                    switch ($parameter_allowed_key){
                                        case 'Взрослым':
                                            $product_new->allowed_adult = $parameter_allowed_value;
                                            break;
                                        case 'Детям':
                                            $product_new->allowed_child = $parameter_allowed_value;
                                            break;
                                        case 'Беременным':
                                            $product_new->allowed_pregnant = $parameter_allowed_value;
                                            break;
                                        case 'Кормящим':
                                            $product_new->allowed_nursing = $parameter_allowed_value;
                                            break;
                                        case 'Аллергикам':
                                            $product_new->allowed_allergic = $parameter_allowed_value;
                                            break;
                                        case 'Диабетикам':
                                            $product_new->allowed_diabetic = $parameter_allowed_value;
                                            break;
                                        case 'Водителям':
                                            $product_new->allowed_driver = $parameter_allowed_value;
                                            break;
                                        default:
                                            throw new \Exception('UNKNOWN ALLOWED TYPE! '.$parameter_allowed_key);
                                            break;
                                    }
                                }
                            }
                            */

                            // Brand
                            $title_short = $searched_item['data'][1]['text'] ?? null;
                            $product_new->title_short = $title_short;

                            if(isset($parameters['Фасовка']['value'])){
                                $product_new->packing = $parameters['Фасовка']['value'];
                            }
                            if(isset($parameters['Форма выпуска']['value'])){
                                $product_new->release_form = $parameters['Форма выпуска']['value'];
                            }
                            if(isset($parameters['Упаковка']['value'])){
                                $product_new->packaging_type = $parameters['Упаковка']['value'];
                            }




                            /*if(isset($parameters['Способ введения']['value'])){
                                $product_new->administration = $parameters['Способ введения']['value'];
                            }*/
                            /*if(isset($parameters['Количество в упаковке']['value'])){
                                $product_new->count_in_package = $parameters['Количество в упаковке']['value'];
                            }*/
                            /*if(isset($parameters['Взаимодействие с едой']['value'])){
                                $product_new->food_interaction = $parameters['Взаимодействие с едой']['value'];
                            }*/
                            /*if(isset($parameters['Чувствительность к свету']['value'])){
                                $product_new->light_sensitivity = $parameters['Чувствительность к свету']['value'];
                            }*/
                            /*if(isset($parameters['Условия отпуска']['value'])){
                                $product_new->conditions_of_supply = $parameters['Условия отпуска']['value'];
                            }*/
                            /*if(isset($parameters['Срок годности']['value'])){
                                $product_new->expiration_date = $parameters['Срок годности']['value'];
                            }*/
                            /*if(isset($parameters['Температура хранения']['value'])){
                                $product_new->storage_temperature = $parameters['Температура хранения']['value'];
                            }*/
                            /*if(isset($parameters['Производитель']['value'])){
                                $product_new->manufacturer = $parameters['Производитель']['value'];
                            }*/
                            /*if(isset($parameters['Страна владелец лицензии']['value'])){
                                $product_new->country_license_holder = $parameters['Страна владелец лицензии']['value'];
                            }*/
                            /*if(isset($parameters['Код АТС']['value'][0])){
                                $product_new->atc_code = $parameters['Код АТС']['value'][0];
                            }*/

                            $product_new->parsed_source = $product_url;

                            $product_image = [];
                            $img_counter = 0;
                            foreach ($images_urls as $images_url){
                                $folder_thousand = intdiv((int)$product_new->id, 1000) * 1000;
                                $folder_thousand = $folder_thousand.'-'.($folder_thousand+999);
                                $folder_hundred = intdiv((int)$product_new->id, 100) * 100;
                                $folder_hundred = $folder_hundred.'-'.($folder_hundred+99);
                                $filepath = 'products/'.$folder_thousand.'/'.$folder_hundred.'/'.'prod_'.$product_new->id;
                                $filename = 'prod_img_'.$product_new->id.'_'.$img_counter;
                                $result = $parser->download_image($images_url, $image_base_path.'/'.$filepath, $filename);

                                if($result !== false){
                                    $product_image[] = $filepath.'/'.$result;
                                    if($img_counter === 0){
                                        $product_new->image_thumb = $filepath.'/'.$result;
                                    }
                                }else{
                                    echo "image failed! (".$images_url.")".PHP_EOL;
                                }
                                $product_new->image = json_encode($product_image, JSON_UNESCAPED_SLASHES);

                                $img_counter++;
                            }
                            $product_new->save();

                        }catch (\Exception $exception){
                            $product_new->delete();
                            dd($exception->getMessage());
                        }

                        $item_count++;
                    }

                    echo "PAGE FINISHED ".$page_counter.PHP_EOL;
                    $page_counter++;
                }
            }
            echo "CATEGORY LVL1 FINISHED ".$cat_level_1_name.PHP_EOL;
        }

        echo 'FINISHED. $item_count = '.$item_count.PHP_EOL;
        echo "</pre>";
    }

    function get_archive($config = array(), $base_url = null){
        $result = [];
        $url = $config['url'];
        $parser = new HtmlParser();
        $html = $parser->get_get_content($url);
        $result['html'] = $html;

        // Get categories
        $categories = null;
        if(isset($config['categories'])){
            $parts = [
                'categories' => $config['categories']
            ];
            $categories = $parser->get_parts($html, $parts, $base_url);
        }
        $result['categories'] = $categories['categories'];

        return $result;
    }

    // Items
    function get_items($url, $config = array(), $base_url = null){
        $parser = new HtmlParser();
        $html = $parser->get_get_content($url);


        if(isset($config['items'])){
            $parts = [
                'items' => $config['items'],
            ];
            $items = $parser->get_parts($html, $parts, $base_url);
            $result = $items['items'];
        }else{
            $result = null;
        }
        return $result;
    }

    // Pagination
    function get_pagination($html, $config = array(), $base_url = null){
        $result = [];
        $parser = new HtmlParser();

        if(isset($config['pagination'])){
            $parts = [
                'pagination' => $config['pagination'],
            ];
            $pagination = $parser->get_parts($html, $parts, $base_url);
            $result['pagination'] = $pagination['pagination'];
        }else{
            $result['pagination'] = null;
        }
        return $result;
    }

    function get_archive_js($config = array()){
        $url = $config['url'];
        $phantom_folder = '/home/priadigi/_phantom_js';
        $cookies_path = base_path().'/data/pjs_cookies1.txt';
        putenv('TMPDIR=/home/priadigi/tmp');

        $client = Client::getInstance();
        $client->getEngine()->setPath($phantom_folder.'/bin/phantomjss');
        //$client->getEngine()->debug(true);
        //$client->getEngine()->addOption('--load-images=true');
        $client->getEngine()->addOption('--cookies-file='.$cookies_path);
        $client->isLazy();

        $request = $client->getMessageFactory()->createRequest($url, 'GET');
        //$request = $client->getMessageFactory()->createRequest('https://www.google.com/', 'GET');
        //$request = $client->getMessageFactory()->createRequest('https://www.olx.ua/nedvizhimost/kvartiry-komnaty/prodazha-kvartir-komnat/kvartira/donetsk/?search%5Bprivate_business%5D=private&currency=USD', 'GET');
        $request->setDelay(3);
        $request->setTimeout(5000);
        $request->setHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.98 Safari/537.36'
        ]);
        $request->setViewportSize(1366, 768);

        $response = $client->getMessageFactory()->createResponse();

        // Send the request
        $client->send($request, $response);

        dd($response);

        if($response->getStatus() === 200) {
            // Dump the requested page content
            //echo $response->getContent();
        }else{
            //echo $response->getContent();
        }
    }

}








