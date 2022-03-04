<?php


namespace App\Http\Controllers\Api;

use App\Imports\UniImport;
use App\Jobs\ProductsAccordanceJob;
use App\Jobs\SyncAvailabilityJob;
use App\Jobs\SyncProductsJob;
use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\ShopLocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SynchronizerController
{

    public function test(){

    }

    public function xls_sync(){
        $product_list["filename"] = "product_list.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        Excel::load($product_list["filepath"], function($excel) {
            $excel->sheet(0, function($sheet){
                $excel_products_arr = $sheet->toArray();
                dd($excel_products_arr);
            });
        });

    }

    // See xml data examples at bottom

    // =====================================================
    // =    INIT
    // =====================================================
    public $shops_map = null;
    public $shops_site = null;

    // Goal: [
    //  {xml shop index} => [
    //      "id" => {site shop id}
    //      "object" => {model}
    //  ]
    //]

    // =====================================================
    // =    FUNCTIONAL
    // =====================================================

    public static function create_products_sync_job(){
        dispatch(new SyncProductsJob());
    }

    public static function create_availability_sync_job(){
        dispatch(new SyncAvailabilityJob());
    }

    public static function create_products_accordance_job(){
        dispatch(new ProductsAccordanceJob());
    }

    public static function products_sync(){
        //echo "<pre>";

        // Define paths and other vars
        $product_list["filename"] = "product_list.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];

        // Skip indexes (null if none)
        $skip_indexes = [0];

        // Iterate
        foreach ($rows as $row_index => $row){
            // Skips
            if($skip_indexes !== null){
                if(array_search($row_index, $skip_indexes) !== false){
                    continue;
                }
            }

            // Normalize
            $row[1] = trim(preg_replace("/\s{2,}/", " ", $row[1]));
            $row[5] = trim(preg_replace("/\s{2,}/", " ", $row[5]));

            // Create or Update
            $sku = $row[0];
            $object = Product::query()
                ->where("sku", "=", $sku)
                ->first()
            ;

            /*
            0 => sku
            1 => title
            2 => release_form
            3 => count_in_package
            4 => brand
            5 => trade_title
            */

            // Update
            if($object !== null) {
//                $object->title = $row[1];
//                $object->release_form = $row[2];
//                $object->count_in_package = $row[3];
//                $object->brand = $row[4];
//                $object->trade_title = $row[5];
//                $object->save();
            }
            // Create
            else{
                $object = new Product();
                $object->sku = $sku;
                $object->title = $row[1];
                $object->release_form = $row[2];
                $object->count_in_package = $row[3];
                $object->brand = $row[4];
                $object->trade_title = $row[5];
                $object->save();
            }
        }

        //echo "</pre>";

    }

    // Download availability file from FTP
    public static function get_sync_file_availability(){
        $connection = Storage::disk('ftp');
        $fileslist = $connection->allFiles();
        $filename = $fileslist[0];

        $content = $connection->get($filename);
        $result = Storage::disk('root')->put("data/import/_availability.xls", $content);
        return $result;
    }

    public static function availability_sync(){
        //echo "<pre>";

        $debug = true;
        if($debug){
            $info = [];
            $info["row_index"] = 0;
            $info["product_sku_found"] = 0;
            $info["product_sku_not_found"] = 0;
            $info["sku_null_count"] = 0;
            $info["product_null_count"] = 0;
            $info["product_found_count"] = 0;
            $info["availability_created_count"] = 0;
            $info["availability_updated_count"] = 0;
            $info["availability_same_count"] = 0;
        }

        // Define paths and other vars
        $product_list["filename"] = "_availability.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        // Get shops
        $shops = ShopLocation::query()
            ->get()
        ;

        $shop_row_indexes = [
            1 => 10,
            2 => 44,
            3 => 24,
            4 => 28,
            5 => 40,
            6 => 18,
            7 => 46,
            8 => 22,
            9 => 38,
            10 => 20,
            11 => 14,
            12 => 36,
            13 => 16,
            14 => 12,
            15 => 48,
            16 => 8,
            17 => 26,
            18 => 30,
            19 => 32,
            20 => 34,
            21 => 42,
            22 => 50,
        ];


        //self::get_shops_site();

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];

        // Skip indexes (null if none)
        $skip_indexes = [0, 1, 2, 3];

        // Iterate
        foreach ($rows as $row_index => $row){
            // Skips
            if($skip_indexes !== null){
                if(array_search($row_index, $skip_indexes) !== false){
                    continue;
                }
            }

            if($row_index % 100 === 0){
                print_r($info);
            }

            if($debug){
                $info["last_row_index"] = $row_index;
                $info["last_row"] = $row;
            }



            /*
            1 => sku
            7 => price
            8 => 16 Аптека №18 (Шахтостроителей)
            10 => 1
            12 => 14
            14 => 11
            16 => 13
            18 => 6
            20 => 10
            22 => 8
            24 => 3
            26 => 17 Аптека №16 (Ленинский 47г)
            28 => 4
            30 => 18 Аптека №14 (пл.Победы, 31)
            32 => 19 Аптека №17 (к-л,Железнодорожный,37)
            34 => 20 Аптека №21 (Щетинина) (точный адрес)
            36 => 12
            38 => 9
            40 => 5
            42 => 21 Аптека №19 (ОсвобождениеДонбасса)
            44 => 2
            46 => 7
            48 => 15
            50 => 22 Подразд.ФЛП Тимченко А.Г. Пушкина,7б
            */

            $sku = $row[1];
            if($sku === null){
                if($debug){
                    $info["sku_null_count"]++;
                }
                continue;
            }


            // Get product
            $product = Product::query()
                ->where("sku", "=", $sku)
                ->first()
            ;
            if($product === null){
                if($debug){
                    $info["product_null_count"]++;
                }
                continue;
            }

            if($debug){
                $info["product_found_count"]++;
            }


            // Update price
            $price = (int)str_replace([",", " "], "", $row[7]);
            $product->price = $price;
            $product->save();

            foreach ($shops as $shop){
                // Get Availability value
                $shop_row_index = $shop_row_indexes[$shop->id];
                $avail_value = $row[$shop_row_index] ?? null;
                if($avail_value === null){
                    continue;
                }
                if($avail_value === "Да"){
                    $avail_value = 1;
                }else{
                    $avail_value = 0;
                }

                // Get Availability
                $avail = ProductAvailability::query()
                    ->in_shop($shop->id)
                    ->in_product($product->id)
                    ->first()
                ;

                // Create
                if($avail === null){
                    $avail = new ProductAvailability();
                    $avail->product_id = $product->id;
                    $avail->shop_location_id = $shop->id;
                    $avail->available = $avail_value;
                    $avail->save();
                    if($debug){
                        $info["availability_created_count"]++;
                    }
                }
                // Update
                else{
                    if($avail->available !== $avail_value){
                        $avail->available = $avail_value;
                        $avail->save();
                        if($debug){
                            $info["availability_updated_count"]++;
                        }
                    }else{
                        if($debug){
                            $info["availability_same_count"]++;
                        }
                    }
                }
            }
        }

        if($debug){
            dd($info);
        }
        //echo "</pre>";

    }

    public static function products_accordance(){
        $info = [];
        $info["full_matches"] = [];
        $info["products_index"] = 0;

        $old_base_path = "/var/www/olv_site/data/www/apteka-olvia.com.ua";
        $current_base_path = base_path();


//        $products_old = Product::on("mysql_old")
//            ->get()
//        ;

        $products = Product::query()
            //->limit(1000)
            //->offset(0)
            //->where("id", 2760)
            //->whereNotNull("compared_old_id")
            ->get()
        ;

        echo "TOTAL PRODUCTS: ". $products->count() . PHP_EOL;

        foreach ($products as $pindex => $product){

            $product_title = $product->title;

            // Normalize
            $product_title = str_replace([".", ",", ":"], " ", $product_title);
            $product_title_parts = explode(" ", $product_title);
            foreach ($product_title_parts as $index => $value){
                if(mb_strlen($value) < 4){
                    unset($product_title_parts[$index]);
                }
            }

            $products_old = Product::on("mysql_old");
            foreach ($product_title_parts as $search_part){
                $products_old = $products_old->where("title", "LIKE", "%".$search_part."%");
            }
            $products_old = $products_old->get();

            if($products_old->count() === 1){
                $info["full_matches"][] = [
                    "old_id" => $products_old[0]->id,
                    "old_title" => $products_old[0]->title,
                    "new_id" => $product->id,
                    "new_title" => $product->title,
                ];
                //dd($product_title, $product_title_parts, $products_old);

                // Update
                $product->compared_old_id = $products_old[0]->id;
                $product->meta_title = $products_old[0]->meta_title;
                $product->meta_description = $products_old[0]->meta_description;
                $product->meta_h1 = $products_old[0]->meta_h1;
                $product->excerpt = $products_old[0]->excerpt;
                $product->body = $products_old[0]->body;
//                $product->image = $products_old[0]->image;
//                $product->image_thumb = $products_old[0]->image_thumb;
                $product->category_id = $products_old[0]->category_id;
                $product->title_short = $products_old[0]->title_short;
                $product->administration = $products_old[0]->administration;
                $product->food_interaction = $products_old[0]->food_interaction;
                $product->light_sensitivity = $products_old[0]->light_sensitivity;
                $product->conditions_of_supply = $products_old[0]->conditions_of_supply;
                $product->expiration_date = $products_old[0]->expiration_date;
                $product->storage_temperature = $products_old[0]->storage_temperature;
                $product->manufacturer = $products_old[0]->manufacturer;
                $product->country_license_holder = $products_old[0]->country_license_holder;
                $product->atc_code = $products_old[0]->atc_code;
                $product->parsed_source = $products_old[0]->parsed_source;
                $product->allowed_adult = $products_old[0]->allowed_adult;
                $product->allowed_child = $products_old[0]->allowed_child;
                $product->allowed_pregnant = $products_old[0]->allowed_pregnant;
                $product->allowed_nursing = $products_old[0]->allowed_nursing;
                $product->allowed_allergic = $products_old[0]->allowed_allergic;
                $product->allowed_diabetic = $products_old[0]->allowed_diabetic;
                $product->allowed_driver = $products_old[0]->allowed_driver;
                $product->packing = $products_old[0]->packing;
                $product->save();

                // Copy images
                // -- Full
                $images = json_decode($products_old[0]->image_thumb) ?? $products_old[0]->image_thumb;
                if(!empty($images)){
                    if(!is_array($images)){
                        $images = [$images];
                    }

                    //$product_old_path = $old_base_path . '/public/uploads/products/' . split_folders_by_id($products_old[0]->id, true) . '/prod_'.$products_old[0]->id;
                    //$product_old_path = $images
                    //$product_current_path = $current_base_path . '/public/uploads/products/' . split_folders_by_id($products_old[0]->id, true).'/'.$products_old[0]->id;

                    foreach ($images as $image_index => $image){
                        //$product_old_path = $old_base_path . '/public/uploads/' . split_folders_by_id($products_old[0]->id, true) . '/prod_'.$products_old[0]->id;
                        $image_old_pathname = $old_base_path . '/public/uploads/' . $image;
                        $image_pathinfo = pathinfo($image_old_pathname);

                        $image_current_uploads_pathname = 'products/' . split_folders_by_id($product->id, true).'/'.$product->id . '/' . $image_index.".".strtolower($image_pathinfo["extension"]);
                        $image_current_full_pathname = $current_base_path . '/public/uploads/' . $image_current_uploads_pathname;

                        $image_current_full_pathinfo = pathinfo($image_current_full_pathname);
                        if(!File::exists($image_current_full_pathinfo["dirname"])){
                            File::makeDirectory($image_current_full_pathinfo["dirname"], 0755, true);
                        }
                        File::copy($image_old_pathname, $image_current_full_pathname);
                    }

                    // Update images
                    $product->image = json_encode([$image_current_uploads_pathname], JSON_UNESCAPED_SLASHES);
                    $product->image_thumb = $image_current_uploads_pathname;
                    $product->save();
                }
            }

            if($pindex % 100 === 0){
                echo "LAST FINISHED INDEX: ".$pindex . PHP_EOL;
                echo "FOUND: ". count($info["full_matches"]) . PHP_EOL;
            }
        }

        //Log::info("[ACCORD] ".json_encode($info));
        dd(count($info["full_matches"]), $info);
    }

    // === Version 2 === //
    public static function availability_sync_mode2(){
        //echo "<pre>";

        $debug = true;
        if($debug){
            $info = [];
            $info["sku_null_count"] = 0;
            $info["product_created_count"] = 0;
            $info["product_updated_count"] = 0;
            $info["availability_created_count"] = 0;
            $info["availability_updated_count"] = 0;
            $info["availability_same_count"] = 0;
        }

        // Define paths and other vars
        $product_list["filename"] = "_availability.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        // Get shops
        $shops = ShopLocation::query()
            ->get()
        ;

        $shop_row_indexes = [
            1 => 10,
            2 => 44,
            3 => 24,
            4 => 28,
            5 => 40,
            6 => 18,
            7 => 46,
            8 => 22,
            9 => 38,
            10 => 20,
            11 => 14,
            12 => 36,
            13 => 16,
            14 => 12,
            15 => 48,
            16 => 8,
            17 => 26,
            18 => 30,
            19 => 32,
            20 => 34,
            21 => 42,
            22 => 50,
        ];


        //self::get_shops_site();

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];

        // Skip indexes (null if none)
        $skip_indexes = [0, 1, 2, 3];

        // Iterate
        foreach ($rows as $row_index => $row){
            // Skips
            if($skip_indexes !== null){
                if(array_search($row_index, $skip_indexes) !== false){
                    continue;
                }
            }

            if($row_index < 15000){
                continue;
            }

            if($row_index % 100 === 0){
                print_r($info);
            }

            if($debug){
                $info["last_row_index"] = $row_index;
                $info["last_row"] = $row;
            }



            /*
            1 => sku
            7 => price
            8 => 16 Аптека №18 (Шахтостроителей)
            10 => 1
            12 => 14
            14 => 11
            16 => 13
            18 => 6
            20 => 10
            22 => 8
            24 => 3
            26 => 17 Аптека №16 (Ленинский 47г)
            28 => 4
            30 => 18 Аптека №14 (пл.Победы, 31)
            32 => 19 Аптека №17 (к-л,Железнодорожный,37)
            34 => 20 Аптека №21 (Щетинина) (точный адрес)
            36 => 12
            38 => 9
            40 => 5
            42 => 21 Аптека №19 (ОсвобождениеДонбасса)
            44 => 2
            46 => 7
            48 => 15
            50 => 22 Подразд.ФЛП Тимченко А.Г. Пушкина,7б
            */

            $sku = $row[1];

            if($sku === null){
                if($debug){
                    $info["sku_null_count"]++;
                }
                continue;
            }

            $price = (int)str_replace([",", " "], "", $row[7]);

            // Get product
            $product = Product::query()
                ->where("sku", "=", $sku)
                ->first()
            ;



            // Create or Update Product
            if($product === null){
                $product = new Product();
                $product->sku = $sku;
                $product->title = $row[2];
                $product->price = $price;
                //$object->title = $row[1];
                //$object->release_form = $row[2];
                //$object->count_in_package = $row[3];
                //$object->brand = $row[4];
                //$object->trade_title = $row[5];
                $product->save();
                $info["product_created_count"]++;
            }else{
                $product->price = $price;
                $product->save();
                $info["product_updated_count"]++;
            }


            foreach ($shops as $shop){
                // Get Availability value
                $shop_row_index = $shop_row_indexes[$shop->id];
                $avail_value = $row[$shop_row_index] ?? null;
                if($avail_value === null){
                    continue;
                }
                if($avail_value === "Да"){
                    $avail_value = 1;
                }else{
                    $avail_value = 0;
                }

                // Get Availability
                $avail = ProductAvailability::query()
                    ->in_shop($shop->id)
                    ->in_product($product->id)
                    ->first()
                ;

                // Create
                if($avail === null){
                    $avail = new ProductAvailability();
                    $avail->product_id = $product->id;
                    $avail->shop_location_id = $shop->id;
                    $avail->available = $avail_value;
                    $avail->save();
                    if($debug){
                        $info["availability_created_count"]++;
                    }
                }
                // Update
                else{
                    if($avail->available !== $avail_value){
                        $avail->available = $avail_value;
                        $avail->save();
                        if($debug){
                            $info["availability_updated_count"]++;
                        }
                    }else{
                        if($debug){
                            $info["availability_same_count"]++;
                        }
                    }
                }
            }
        }

        if($debug){
            dd($info);
        }
        //echo "</pre>";

    }

    public static function products_sync_mode2(){
        //echo "<pre>";
        $info = [];
        $info["products_updated"] = 0;
        $info["products_not_found"] = 0;
        $info["last_row_index"] = 0;

        // Define paths and other vars
        $product_list["filename"] = "product_list.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];

        // Skip indexes (null if none)
        $skip_indexes = [0];

        // Iterate
        foreach ($rows as $row_index => $row){
            // Skips
            if($skip_indexes !== null){
                if(array_search($row_index, $skip_indexes) !== false){
                    continue;
                }
            }

            if($row_index % 100 === 0){
                print_r($info);
            }

            // Normalize
            $row[1] = trim(preg_replace("/\s{2,}/", " ", $row[1]));
            $row[5] = trim(preg_replace("/\s{2,}/", " ", $row[5]));

            // Create or Update
            $sku = $row[0];
            $object = Product::query()
                ->where("sku", "=", $sku)
                ->first()
            ;

            /*
            0 => sku
            1 => title
            2 => release_form
            3 => count_in_package
            4 => brand
            5 => trade_title
            */

            // Update
            if($object !== null) {
                $object->title = $row[1];
                $object->release_form = $row[2];
                $object->count_in_package = $row[3];
                $object->brand = $row[4];
                $object->trade_title = $row[5];
                $object->status = Product::SYNCED_CATALOG;
                $object->save();

                $info["products_updated"]++;
            }
            // Create
            else{
                $info["products_not_found"]++;
                //continue; // Skip
//                $object = new Product();
//                $object->sku = $sku;
//                $object->title = $row[1];
//                $object->release_form = $row[2];
//                $object->count_in_package = $row[3];
//                $object->brand = $row[4];
//                $object->trade_title = $row[5];
//                $object->save();
            }

            $info["last_row_index"] = $row_index;
        }

        //echo "</pre>";

        dd("FINISHED", $info);
    }


    // =====================================================
    // =    TESTS
    // =====================================================

    public function test_ftp_connection(){
        $connection = Storage::disk('ftp');
        $fileslist = $connection->allFiles();
        $filename = $fileslist[0];

        $content = $connection->get($filename);
        //$result = Storage::disk('root')->put("data/import/_availability.xls", $content);
        dd($fileslist);
    }

    public function test_products_list(){
        echo "<pre>";

        // Define paths and other vars
        $product_list["filename"] = "product_list.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];
        dd($rows);

        // Iterate
        foreach ($rows as $row_index => $row){
            if($row_index > 20){
                break;
            }

            var_dump($row);
            echo "\n\n";
        }

        echo "</pre>";
    }

    public function test_availability(){
        //echo "<pre>";
        $info = [];
        $info["rows"] = [];
        $info["not_founds"] = [];
        $info["product_null_count"] = 0;
        $info["product_found_count"] = 0;
        $info["lowest_sku_found"] = 999999;
        $info["highest_sku_found"] = 0;
        $info["lowest_sku_not_found"] = 999999;
        $info["highest_sku_not_found"] = 0;



        // Define paths and other vars
        $product_list["filename"] = "_availability.xls";
        $product_list["filepath"] = base_path('data/import').'/'.$product_list["filename"];

        // Get lists
        $lists = Excel::toArray(new UniImport(), $product_list["filepath"]);

        // Get first list rows
        $rows = $lists[0];

        $info["total_rows"] = count($rows);

        // Iterate
        foreach ($rows as $row_index => $row){
//            if($row_index > 20){
//                break;
//            }

            if($row_index < 3000){
                continue;
            }
            if($row_index > 3500){
                break;
            }

            $sku = $row[1];
            if($sku === null){
                $info["sku_null_count"]++;
                continue;
            }


            // Get product
            $product = Product::query()
                ->where("sku", "=", $sku)
                ->first()
            ;
            if($product === null){
                $info["product_null_count"]++;
                $info["not_founds"][] = [
                    "av_sku" => $sku,
                    "av_title" => $row[2],
                ];
                if($sku < $info["lowest_sku_not_found"]){
                    $info["lowest_sku_not_found"] = $sku;
                }
                if($sku > $info["highest_sku_not_found"]){
                    $info["highest_sku_not_found"] = $sku;
                }
                continue;
            }

            $info["product_found_count"]++;
            if($sku < $info["lowest_sku_found"]){
                $info["lowest_sku_found"] = $sku;
            }
            if($sku > $info["highest_sku_found"]){
                $info["highest_sku_found"] = $sku;
            }
        }

        dd($info);

        //echo "</pre>";
    }

}

/**
Products list

Header
array(6) {
[0]=>
string(6) "Код"
[1]=>
string(24) "Наименование"
[2]=>
string(25) "Форма выпуска"
[3]=>
string(20) "Кол-во в уп."
[4]=>
string(10) "Бренд"
[5]=>
string(24) "Номенклатура"
}

Row
array(6) {
[0]=>
int(7289)
[1]=>
string(93) " Oral-B насадка для зубной щетки  3D WHITE отбеливающая №2"
[2]=>
string(14) "Насадка"
[3]=>
int(2)
[4]=>
string(6) "Oral B"
[5]=>
string(93) "З/щ Oral-B насадка для з/щ 3D WHITE отбеливающая №2 *7757 ТП-5%"
}

 */

/**
Availability shops

[8]=>
string(51) "Аптека №18 (Шахтостроителей)"
[9]=>
NULL
[10]=>
string(54) "Аптека №1 (Красноармейская, 56)"
[11]=>
NULL
[12]=>
string(49) "Аптека №12 (Островского,70/20)"
[13]=>
NULL
[14]=>
string(52) "Аптека №15 (250-лет Донбасса 5в)"
[15]=>
NULL
[16]=>
string(37) "Аптека №13 (Енакиево)"
[17]=>
NULL
[18]=>
string(40) "Аптека №6 (ул. Пухова,1)"
[19]=>
NULL
[20]=>
string(44) "Аптека №9 (Ферганская, 41)"
[21]=>
NULL
[22]=>
string(50) "Аптека №7 (50-летия СССР, 155-б)"
[23]=>
NULL
[24]=>
string(58) "Аптека №2 (Римского-Корсакова, 2)"
[25]=>
NULL
[26]=>
string(44) "Аптека №16 (Ленинский 47г)"
[27]=>
NULL
[28]=>
string(44) "Аптека №3 (Раздольная, 15)"
[29]=>
NULL
[30]=>
string(42) "Аптека №14 (пл.Победы, 31)"
[31]=>
NULL
[32]=>
string(60) "Аптека №17 (к-л,Железнодорожный,37)"
[33]=>
NULL
[34]=>
string(37) "Аптека №21 (Щетинина)"
[35]=>
NULL
[36]=>
string(37) "Аптека №11 (Горловка)"
[37]=>
NULL
[38]=>
string(43) "Аптека №4 (ул. Артёма, 183)"
[39]=>
NULL
[40]=>
string(67) "Аптека №8 (230-й Стрелковой Дивизии, 9а)"
[41]=>
NULL
[42]=>
string(61) "Аптека №19 (ОсвобождениеДонбасса)"
[43]=>
NULL
[44]=>
string(45) "Аптека №10 (Терешкова, 15б)"
[45]=>
NULL
[46]=>
string(44) "Аптека №5 (Коммунаров, 52)"
[47]=>
NULL
[48]=>
string(75) "Аптека №1 Медизделия (Красноармейская, 56)"
[49]=>
NULL
[50]=>
string(64) "Подразд.ФЛП Тимченко А.Г. Пушкина,7б"

 */