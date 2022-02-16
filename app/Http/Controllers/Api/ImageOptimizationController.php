<?php
/**
 * Created by PhpStorm.
 * User: Ancient
 * Date: 2020/02/20
 * Time: 14:06
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Image as Image;
use Illuminate\Http\Request;



class ImageOptimizationController extends Controller
{

    // Clear images table and all cache
    public function clear_cached_images(Request $request){

        $tag = $request->query('tag');

        // Clear images table
        $images = Image::query();
        if($tag !== null){
            $images = $images->where('tag', 'like', '%'.$tag.'%');
        }
        $images->delete();

        // Clear cache
        \Artisan::call('cache:clear');

        return '<h1>Cached images in DB and all cache deleted</h1>';
    }

    public function mass_optimize_products(Request $request){
        save_config(['running' => true], 'auto_image_optimizer' );
        echo '<pre>';
        $time_start = microtime(true);


        $last_id = $request->query('last_id', config('auto_image_optimizer.last_id', -1));
        $limit = (int)$request->query('limit', -1);

        $items = Product::query()
            ->limit($limit)
            ->published()
            ->get();


        $glob_index = -1;
        $glob_count = 0;
        foreach ($items as $item){
            $glob_index++;

            if($limit <= $glob_count && $limit != -1){
                break;
            }

            if($item->id <= $last_id){
                continue;
            }
            //echo 'STARTED '.$item->id.PHP_EOL;

            // Thumbs
            $image = $item["image_thumb"] ?? '';
            if(empty($image)){
                $image = $item->no_image ?? '';
            }
            if(!empty($image)){
                $sizes = array(
                    '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 90, 'watermark' => config('image.watermark.product')],
                    'id' => 'product_img_'.$item->id,
                );
                $mappings = array(
                    '>320' => '1',
                    'default' => '1'
                );
                $sizes = \Img::img($sizes);
                $picture = \Img::picture_compose($sizes, $mappings, true, '', '', true);
            }

            // Images
            $images = json_decode($item["image"], true) ?? '';
            if(!empty($images)){
                foreach ($images as $image)
                    $sizes = array(
                        '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90, 'watermark' => config('image.watermark.product')],
                        'id' => 'product'.$image,
                        'get_dimensions' => true
                    );
                    $mappings = array(
                        '>320' => '1',
                        'default' => '1'
                    );

                    $sizes = \Img::img($sizes);
                    $picture = \Img::picture_compose($sizes, $mappings, [
                        'is_hidden' => false,
                        'classes' => '',
                        'alt' => '',
                        'is_lazy' => true,
                        'fullscreen' => true,
                        'placeholder' => [
                            'plain' => asset('uploads/product_image_placeholder.png'),
                        ]
                    ]);
            }

            save_config(['last_id' => $item->id], 'auto_image_optimizer' );
            save_config(['last_index' => $glob_index], 'auto_image_optimizer' );
            $glob_count++;
            $time = microtime(true) - $time_start;
            save_config(['time' => $time], 'auto_image_optimizer' );
            echo $time.PHP_EOL;
            if($time > 180){
                break;
            }

        }

        save_config(['running' => false], 'auto_image_optimizer' );

        echo PHP_EOL.'FINISHED'.PHP_EOL;
        echo '</pre>';
    }

    public function target_optimize_products(Request $request){
        echo '<pre>';

        $page_limit = 10;
        $page = $request->query('page', config('auto_image_optimizer.target_last_page'));
        $last_page = config('auto_image_optimizer.target_max_page', null);
        $page_process_limit = $page + $page_limit;

        $categories = [
            2,      // Аллергия
            7,      //Боль
            12,     // Вредные привычки
        ];
        if($page <= $last_page || $last_page === null){
            for (; $page <= $page_process_limit; $page++){
                $items = Product::macro_get_products(2, $page);
                if($last_page === null){ // First time
                    $last_page = $items->lastPage();
                    save_config(['target_max_page' => $last_page], 'auto_image_optimizer' );
                }

                foreach ($items as $item){
                    echo 'STARTED '.$item->id.PHP_EOL;

                    // Thumbs
                    $image = $item["image_thumb"] ?? '';
                    if(!empty($image)){
                        $sizes = array(
                            '1' => ['width' => 540, 'relative_path' => 'uploads/'.$image, 'q'=> 90, 'watermark' => config('image.watermark.product')],
                            'id' => 'product_img_'.$item->id,
                        );
                        $mappings = array(
                            '>320' => '1',
                            'default' => '1'
                        );
                        $sizes = \Img::img($sizes);
                        $picture = \Img::picture_compose($sizes, $mappings, true, '', '', true);
                    }

                    // Images
                    $images = json_decode($item["image"], true) ?? '';
                    if(!empty($images)){
                        foreach ($images as $image){
                            $sizes = array(
                                '1' => ['width' => 376, 'relative_path' => 'uploads/'.$image, 'q'=> 90, 'watermark' => config('image.watermark.product')],
                                'id' => 'product'.$image,
                                'get_dimensions' => true
                            );
                            $mappings = array(
                                '>320' => '1',
                                'default' => '1'
                            );
                            $sizes = \Img::img($sizes);

                            $picture = \Img::picture_compose($sizes, $mappings, [
                                'is_hidden' => false,
                                'classes' => '',
                                'alt' => '',
                                'is_lazy' => true,
                                'fullscreen' => true,
                                'placeholder' => [
                                    'plain' => asset('uploads/product_image_placeholder.png'),
                                ]
                            ]);
                        }

                    }

                    echo 'ENDED '.$item->id.PHP_EOL.PHP_EOL;
                }
            }
            $page--;
        }else{
            echo 'CATEGORY FINISHED'.PHP_EOL;
        }


        save_config(['target_last_page' => $page], 'auto_image_optimizer' );

        echo PHP_EOL.'FINISHED'.PHP_EOL;
        echo '</pre>';
    }

}