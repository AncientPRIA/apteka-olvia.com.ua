<?php
/**
 * Created by PhpStorm.
 * User: Ancient
 * Date: 2020/04/28
 * Time: 10:24
 */

namespace App\Http\Controllers\Api;

use App\Processors\CSV_Generator;
use VK_SDK;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;
//use Illuminate\Http\Request;



class VKController
{

    public function verify(){

    }

    public function export_xls(){
        //$xls_path = base_path('data').'/konfeterra_winestyle_matches2.xlsx';
        $xls_path = base_path('data').'/1.xlsx'; // tihie
        //$xls_path = base_path('data').'/krep.xlsx'; // krep
        //$xls_path = base_path('data').'/igrs.xlsx'; // igrs

        $csv_path =  base_path('data').'/konfeterra_winestyle_matches3.csv';
        $csv_path =  base_path('data').'/1.csv';

        //$csv_gen = new CSV_Generator();
        //$csv_gen->set_file($csv_path);

        echo '<pre>'.PHP_EOL;

        Excel::load($xls_path, function($excel) {
            $excel->sheet(0, function($sheet){
                // Init
                $image_base_path_check = public_path('uploads/konfeterra_ready_3');
                $image_base_path = public_path('uploads/konfeterra_ready');
                $image_fit_background = public_path('img/vk_white_background.png');
                $app_id = 7456979;//7437045;
                $app_secure_key = 'jY50VTo0SCiL9z8e8GGu';//'Nn5b837TTKh9SaZVDJOg';
                $app_secret_access_key = 'e9509e10e9509e10e9509e1022e92156c3ee950e9509e10b7e61315d833f59d6463f1e1';//'b0a277deb0a277deb0a277de9db0d30d2bbb0a2b0a277deee059da2695863bbba0a5a8d';
                $owner_id = -141388432;//-164559344;
                $group_id = 141388432;//164559344;

                // Limits
                $max_row = 300; // 157 for 1, 47 for 3
                $start_items = 140; // In vk index 90 is last (sku 3126)
                $limit_items = 20;

                // Stats
                $count_viewed = 0;
                $count_skipped = 0;
                $count_total = 0;

                // VK Init
                $vk_api = new VK_SDK();
                $vk_api->set_app_id($app_id);
                $vk_api->set_owner_id($owner_id);
                $vk_api->set_app_secure_key($app_secure_key);
                $vk_api->set_app_secret_access_key($app_secret_access_key);
                // not need
                //$vk_api->set_access_token('a9d02344617c036f77feb56338350e2e54fdc7b388eac743005e921f5b4340a932a973a5dea270cd17af5');

                // Run once and put in browser
                //$access_key_url = $vk_api->get_access_key_url();
                //dd($access_key_url);

                // Set access token retrieved from get_access_key_url()
                $vk_api->set_access_key('06ed43063d36e82c0f411533faea410b48e16f201cdce110f2dc7dadf62dc4bc69c27b8fb16da7bc8e8f9');

                // Test API
                //$test_result = $vk_api->api_test();
                //dd($test_result);


                $excel_products_arr = $sheet->toArray();
                //$excel_products_arr = $csv_gen->get_csv($csv_path);
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

                    $sku = $excel_products_arr[$excel_index][0]; // 0 tihie 2 krep
                    echo PHP_EOL."--------- SKU ".$sku." index ".$excel_index.PHP_EOL;

                    // Verified check
//                    if(strpos($excel_products_arr[$excel_index][0], '*') === false && strpos($excel_products_arr[$excel_index][0], '0') === false){
//                        echo "NOT * OR 0".PHP_EOL;
//                        $count_viewed++;
//                        $count_skipped++;
//                        continue;
//                    }

                    $check_images_folder = $image_base_path.'/'.$sku;
                    $check_images_folder2 = $image_base_path.'/prod_'.$sku;

                    $image_folder = null;
                    if(!file_exists($check_images_folder) && !is_dir($check_images_folder)){
                        //echo "Images not exists".PHP_EOL;
                        //$count_viewed++;
                        //continue;
                        $image_folder = $check_images_folder;
                    }else{
                        $image_folder = $check_images_folder;
                    }

                    if(!file_exists($check_images_folder2) && !is_dir($check_images_folder2)){
                        //echo "Images not exists".PHP_EOL;
                        //$count_viewed++;
                        //continue;
                        $image_folder = $check_images_folder2;
                    }else{
                        $image_folder = $check_images_folder2;
                    }




                    // Prepare poster
                    //$image_folder = $image_base_path.'/prod_'.$sku;
                    if(!is_dir($image_folder)){
                        echo "NO_IMAGES".PHP_EOL;
                        $count_viewed++;
                        $count_skipped++;
                        continue;
                    }
                    $files = File::allFiles($image_folder);
                    if(count($files) === 0){
                        echo "NO_IMAGES".PHP_EOL;
                        $count_viewed++;
                        $count_skipped++;
                        continue;
                    }
                    $poster_path = $files[0]->getPathname();

                    // Prepare gallery
                    $gallery = [];


                    // Prepare data
                    $prepared = array();
                    $prepared['url'] = 'https://olvia.pria.agency/test';
                    $prepared['title'] = $excel_products_arr[$excel_index][1]; // 1 tihie 3 krep
                    // 7 tihie 9 krep
                    $prepared['description'] = strip_tags($excel_products_arr[$excel_index][7]) . "\r\nАртикул: ".leading_symbols($sku, '0', 5);
                    $prepared['description'] = str_replace('.com.ua', '', $prepared['description']);
                    $prepared['price'] = $excel_products_arr[$excel_index][2]; // 2 tihie 4 krep
                    $prepared['category_id'] = 1103;

                    // Add to market
                    try{
                        //dd($prepared);
                        $add_result = $vk_api->add_to_market($group_id, $poster_path, $gallery, $prepared, $image_fit_background);
                        if(!isset($add_result['response']['market_item_id'])){
                            dd($add_result);
                        }
                        sleep(1);
                        $market_id = $add_result['response']['market_item_id'];
                        $add_alb = $vk_api->add_to_album($market_id, 4); // 2 tihie 3 krep
                    }catch (\Exception $exception){
                        echo "Error: ".$exception->getMessage().PHP_EOL;
                        echo $exception->getTraceAsString();
                        break;
                    }



                    echo "SUCCESS".PHP_EOL;


                    $count_viewed++;

                }

                //$sheet->fromArray($results, null, 'A1', false, false);
                //$sheet->fromArray($excel_products_arr, null, 'A1', false, false);
            });

        });//->store('xlsx', $xls_path_e);




        echo '</pre>'.PHP_EOL;
    }

}