<?php


namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Processors\CSV_Generator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
//use Illuminate\Support\Carbon;
//use Illuminate\Support\Str;

class XLSParserController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(Request $request){
        //echo "<pre>";

        $limit = 10;
        $page = 1;

        $xls_path = base_path('data').'/sync.xls';
        $csv_matches_path = base_path('data').'/products_matches.csv';


        $excel_products_arr = Excel::toArray(null, $xls_path);
        //dd($excel_products_arr);

        for ($i = 12; $i < 20; $i++){
            var_dump($excel_products_arr[0][$i]);
        }



        $products = Product::query()
            ->limit($limit)
            ->offset($limit * $page-1)
            ->get();

        $matches = [];

        foreach ($products as $product){
            $matches[$product->id] = [];
            $active_substances = $product->active_substances()->pluck('title')->toArray();
            $matches[$product->id]['product_title'] = $product->title;
            $matched_from_excel = [];
            foreach ($active_substances as $active_substance){
                //$matches[] = array_search($active_substance, $excel_products_arr);

                $matched_from_excel[] = find_in_array($excel_products_arr[0], $active_substance, 2);
            }
            $excel_matches = call_user_func_array('array_merge', $matched_from_excel);
            if(isset($product->title_short)){
                foreach ($excel_matches as $key=>$excel_match)
                {
                    if(mb_stripos($excel_match[3], $product->title_short)){

                    }
                }
            }

            //$matches[$product->id]['matched'] = ;
            if(count($matches[$product->id]) === 0){
                unset($matches[$product->id]);
            }
        }

        //dd($matches);

        $csv_generator = new CSV_Generator();
        $csv_generator->set_file($csv_matches_path);

        foreach ($matches as $product_id=>$product_data){
            $is_first_of_product = true;

            foreach ($product_data['matched'] as $matched_row){
                if($is_first_of_product){
                    $csv_generator->add_row([
                            $product_id,
                            $product_data['product_title'],
                            $matched_row[1],
                            $matched_row[3],
                    ]);
                }else{
                    $csv_generator->add_row([
                        '',
                        '',
                        $matched_row[1],
                        $matched_row[3],
                    ]);
                }

                $is_first_of_product = false;
            }
        }






        echo PHP_EOL."FINISHED";
        echo "</pre>";
    }

    public function img_problem(){
        $skip_to = 70;
        $folder_basepath_old = public_path('uploads/products-r');
        $folder_basepath_new = public_path('uploads/products');

        $products = Product::query()->orderBy('id', 'asc')->get();
        $is_skipping = true;
        foreach ($products as $product){
            if($skip_to === 0){
                $is_skipping = false;
            }
            if($skip_to === $product->id){
                $is_skipping = false;
                continue;
            }
            if($is_skipping){
                continue;
            }
            $images = json_decode($product->image, true);
            //$images_thumb = $product->image_thumb;
            //dd($images, $images_thumb);

            $folder_hundred = intdiv((int)$product->id, 100) * 100;
            $folder_name = $folder_hundred.'-'.((int)$folder_hundred+99) . '/' . 'prod_'.$product->id;
            if(!file_exists($folder_basepath_new.'/'.$folder_name)){
                mkdir($folder_basepath_new.'/'.$folder_name, 0755, true);
            }

            if(isset($images)){
                $is_product_first_image = true;
                foreach ($images as $key=>$image){
                    $info = pathinfo($image);
                    rename($folder_basepath_old.'/'.$info['basename'], $folder_basepath_new.'/'.$folder_name.'/'.$info['basename']);
                    $images[$key] = 'products/'.$folder_name.'/'.$info['basename'];
                    if($is_product_first_image){
                        // Assign as thumb
                        $product->image_thumb = $images[$key];
                    }



                    $is_product_first_image = false;
                }
                $product->image = json_encode($images, JSON_UNESCAPED_SLASHES);
                $product->save();

                //dd($images, json_encode($images, JSON_UNESCAPED_SLASHES), 'last '.$product->id, $product);
            }


            $new_images = '';
            $new_images_thumb = '';
        }

        /*
        $basepath_new = public_path('uploads/products');

        $files = \Storage::disk('public')->files('products-r');
        foreach ($files as $file){
            if(strpos($file, 'prod_') !== false){
                $tmp = explode('_', $file);
                $folder_hundred = intdiv((int)$tmp[1], 100) * 100;
                $folder_segment_1 = $folder_hundred.'-'.((int)$folder_hundred+99);
                $folder_name = $folder_segment_1.'/prod_'.$tmp[1];
                //dd($file, $tmp, $basepath_new.'/'.$folder_name, file_exists($basepath_new.'/'.$folder_name));
                if(!file_exists($basepath_new.'/'.$folder_name)){
                    mkdir($basepath_new.'/'.$folder_name, 0755, true);
                    echo "<div>$folder_name</div>";
                    //dd($basepath_new.'/'.$folder_name);
                }

            }
        }
        */
        //dd($files);
    }
}








