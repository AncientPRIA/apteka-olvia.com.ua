<?php
/**
 * Created by PhpStorm.
 * User: Ancient
 * Date: 2019/08/15
 * Time: 17:02
 */

namespace App\Helpers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File as File;
use Illuminate\Support\Facades\Cache;
use App\Facades\ImageOptimizerFacade as ImgOptimizer;
use App\Models\Image as ImageDB;


class Image
{

    /**
     * $relative_path       String      path from public/
     *
     * */

    public static function img($sizes = array()){

        //$image_url = URL::asset($relative_path);
        // TODO: check is image exists

        /*
        $original_filename = File::name($relative_path); // Basename for name with ext
        $original_ext = File::extension($relative_path);

        $optimized_base_path = public_path('optimized/');
        $optimized_full_path = $optimized_base_path.$relative_path;
        $optimized_directory_full_path = File::dirname($optimized_full_path);

        $res_sizes = array();
        foreach ($sizes as $size){
            $optimized_full_filename_size = $optimized_directory_full_path.'/'.$original_filename.'_'.$size[0];
            if(isset($size[1]) AND $size[1] !== null AND $size[1] !== 0){
                $optimized_full_filename_size .= 'x'.$size[1];
            }
        }
        */

        if(isset($sizes['id'])){
            $sizes = Cache::remember($sizes['id'].'_sizes', 86400, function () use ($sizes){
                $image_in_db = ImageDB::get_item($sizes['id']);
                if($image_in_db !== null){
                    $sizes = json_decode($image_in_db->sizes, true);
                }else{
                    $sizes = ImgOptimizer::optimize_single_image($sizes);
                    $image_in_db = new ImageDB();
                    $image_in_db->key = $sizes['id'];
                    if(isset($sizes['tag'])){
                        $image_in_db->tag = $sizes['tag'];
                    }
                    //$image_in_db->tag =
                    $image_in_db->sizes = json_encode($sizes);
                    $image_in_db->save();
                }
                return $sizes;
            });
        }else{
            $sizes = ImgOptimizer::optimize_single_image($sizes);
        }

        return $sizes;
        //var_dump($sizes);



        /*
        $source_webp = '';

        $source_plain = '';

        $img_last = '';

        $image_tag = '<img class="'.$classes.'" src="'.$image_url.'" alt="'.$alt.'" />';
        */


        //echo $image_tag;

        /*
        GOAL:
            <picture class="LLoad">
                <source srcset="<?=$base_url?>/img/imgsrc.gif" data-original="<?=$base_url?>/img/bg-8.webp" type="image/webp">
                <source srcset="<?=$base_url?>/img/imgsrc.gif" data-original="<?=$base_url?>/img/bg-8.jpg" type="image/jpeg">
                <img src="<?=$base_url?>/img/imgsrc.gif" data-original="<?=$base_url?>/img/bg-8.jpg" alt="Разработка логотипа" class="LLoad new_parallax">
            </picture>

        */
    }


    /*
    <picture>
        <source media="(min-width: 1920px)" type="{{ $sizes['1']['urlset']['webp']['mime'] }}" srcset="{{ $sizes['1']['urlset']['webp']['url'] }}" >
        <source media="(min-width: 1920px)" type="{{ $sizes['1']['urlset']['plain']['mime'] }}" srcset="{{ $sizes['1']['urlset']['plain']['url'] }}" >

        <source media="(min-width: 992px)" type="{{ $sizes['2']['urlset']['webp']['mime'] }}" srcset="{{ $sizes['2']['urlset']['webp']['url'] }}" >
        <source media="(min-width: 992px)" type="{{ $sizes['2']['urlset']['plain']['mime'] }}" srcset="{{ $sizes['2']['urlset']['plain']['url'] }}" >

        <source media="(min-width: 680px)" type="{{ $sizes['3']['urlset']['webp']['mime'] }}" srcset="{{ $sizes['3']['urlset']['webp']['url'] }}" >
        <source media="(min-width: 680px)" type="{{ $sizes['3']['urlset']['plain']['mime'] }}" srcset="{{ $sizes['3']['urlset']['plain']['url'] }}" >

        <source media="(min-width: 1px)" type="{{ $sizes['4']['urlset']['webp']['mime'] }}" srcset="{{ $sizes['4']['urlset']['webp']['url'] }}" >
        <source media="(min-width: 1px)" type="{{ $sizes['4']['urlset']['plain']['mime'] }}" srcset="{{ $sizes['4']['urlset']['plain']['url'] }}" >

        <img src="{{ $sizes['2']['urlset']['plain']['url'] }}" alt="">
      </picture>
    */


    // Generate <picture> element
    public static function picture_compose($sizes, $mappings, $is_hidden = false, $classes = '', $alt = '', $is_lazy = false, $fullscreen = false){
        if(isset($sizes['id'])){
            if(Cache::has($sizes['id'].'_picture_tag')){
                $tag_pucture = Cache::get($sizes['id'].'_picture_tag');
                return $tag_pucture;
            }else{
                $image_in_db = ImageDB::get_item($sizes['id']);
                $tag_pucture = $image_in_db->picture;
                if($tag_pucture !== null){
                    Cache::put($sizes['id'].'_picture_tag', $tag_pucture, 25920000);
                    return $tag_pucture;
                }
            }
        }

        if(is_array($is_hidden)){
            $fullscreen = $is_hidden['fullscreen'] ?? false;
            $is_lazy = $is_hidden['is_lazy'] ?? false;
            $alt = $is_hidden['alt'] ?? '';
            $title = $is_hidden['title'] ?? '';
            $classes = $is_hidden['classes'] ?? '';
            $placeholder_plain = $is_hidden['placeholder']['plain'] ?? asset('img/transparent_placeholder.png');
            //$placeholder_webp = $is_hidden['placeholder']['webp'] ?? asset('img/transparent_placeholder.webp');

            $is_hidden = $is_hidden['is_hidden'] ?? false;
        }else{
            $title = "";
            $placeholder_plain = asset('img/transparent_placeholder.png');
        }



        $lazy_class = '';
        if($is_lazy){
            $lazy_class = ' class="LazyLoad"';
        }

        $tag_pucture = '<picture'.$lazy_class.'>';

        foreach ($mappings as $media => $size_key){

            if($media === 'default'){
                // img last
                $line_style = '';
                if($is_hidden){
                    $line_style = 'style="display: none;"';
                }

                if($classes !== ''){
                    $classes = 'class="'.$classes.'"';
                }

                if($alt !== ''){
                    $title = 'title="'.$alt.'"';
                    $alt = 'alt="'.$alt.'"';
                }


                if(isset($sizes[$size_key]['urlset']['plain']['url'])){

                    $fullscreen_width = '';
                    $fullscreen_height = '';
                    $fullscreen_src = 'data-fullscreen-src="'.$sizes[$size_key]['urlset']['plain']['url'].'"';
                    if($fullscreen){
                        if($sizes[$size_key]['original']['dimensions']['width'] !== 0){
                            $fullscreen_width = 'data-fullscreen-width="'.$sizes[$size_key]['original']['dimensions']['width'].'"';
                        }

                        if($sizes[$size_key]['original']['dimensions']['height'] !== 0){
                            $fullscreen_height = 'data-fullscreen-height="'.$sizes[$size_key]['original']['dimensions']['height'].'"';
                        }

                    }




                    if($is_lazy){
                        $src = 'src="'.$placeholder_plain.'"';
                        $src .= ' data-src="'.$sizes[$size_key]['urlset']['plain']['url'].'"';
                        $src .= ' data-flickity-lazyload="'.$sizes[$size_key]['urlset']['plain']['url'].'"';
                    }else{
                        $src = 'src="'.$sizes[$size_key]['urlset']['plain']['url'].'"';
                    }

                    $line = '<img '.$classes.' '.$src.' '.$fullscreen_src.' '.$fullscreen_width.' '.$fullscreen_height.' '.$alt.' '.$title.' '.$line_style.'>';
                }else{
                    $line = '<img '.$classes.' src="" '.$alt.' '.$title.' '.$line_style.'>';
                }

                $tag_pucture .= $line;

            }else{
                // source
                $sign = $media[0]; // get sign
                $value = substr($media, 1);

                if($sign === '>'){
                    $line_media = 'media="(min-width: '.$value.'px)"';
                }else{
                    $line_media = 'media="(max-width: '.$value.'px)"';
                }

                if(isset($sizes[$size_key]['urlset']['webp']['mime'])){
                    $source_webp_type = 'type="'.$sizes[$size_key]['urlset']['webp']['mime'].'"';
                }else{
                    $source_webp_type = '';
                }

                if(isset($sizes[$size_key]['urlset']['webp']['url'])){

                    if($is_lazy){
                        $source_webp_srcset = 'data-srcset="'
                            .$sizes[$size_key]['urlset']['webp']['url']
                            .', '.$sizes[$size_key]['urlset']['webp2x']['url'].' 2x'
                            .', '.$sizes[$size_key]['urlset']['webp3x']['url'].' 3x'
                            .'"';
                        $source_webp_srcset .= ' data-flickity-lazyload-srcset="'
                            .$sizes[$size_key]['urlset']['webp']['url']
                            .', '.$sizes[$size_key]['urlset']['webp2x']['url'].' 2x'
                            .', '.$sizes[$size_key]['urlset']['webp3x']['url'].' 3x'
                            .'"';
                    }else{
                        $source_webp_srcset = 'srcset="'
                            .$sizes[$size_key]['urlset']['webp']['url']
                            .', '.$sizes[$size_key]['urlset']['webp2x']['url'].' 2x'
                            .', '.$sizes[$size_key]['urlset']['webp3x']['url'].' 3x'
                            .'"';
                    }



                }else{
                    $source_webp_srcset = 'srcset=""';
                }


                if(isset($sizes[$size_key]['urlset']['plain']['mime'])){
                    $source_plain_type = 'type="'.$sizes[$size_key]['urlset']['plain']['mime'].'"';
                }else{
                    $source_plain_type = '';
                }

                if(isset($sizes[$size_key]['urlset']['plain']['url'])){

                    if($is_lazy){
                        $source_plain_srcset = 'data-srcset="'
                            .$sizes[$size_key]['urlset']['plain']['url']
                            .', '.$sizes[$size_key]['urlset']['plain2x']['url'].' 2x'
                            .', '.$sizes[$size_key]['urlset']['plain3x']['url'].' 3x'
                            .'"';
                    }else{
                        $source_plain_srcset = 'srcset="'
                            .$sizes[$size_key]['urlset']['plain']['url']
                            .', '.$sizes[$size_key]['urlset']['plain2x']['url'].' 2x'
                            .', '.$sizes[$size_key]['urlset']['plain3x']['url'].' 3x'
                            .'"';
                    }


                }else{
                    $source_plain_srcset = 'srcset=""';
                }


                $source_webp = '<source '.$line_media.' '.$source_webp_type.' '.$source_webp_srcset.'>';
                $source_plain = '<source '.$line_media.' '.$source_plain_type.' '.$source_plain_srcset.'>';

                $tag_pucture .= $source_webp;
                $tag_pucture .= $source_plain;
            }
        }

        $tag_pucture .= '</picture>';

        if(isset($sizes['id'])){
            $image_in_db = ImageDB::get_item($sizes['id']);
            $image_in_db->picture = $tag_pucture;
            $image_in_db->save();
            Cache::put($sizes['id'].'_picture_tag', $tag_pucture, 86400);
        }

        return $tag_pucture;
    }

    public function img_url($relative_path){

        $image_url = URL::asset($relative_path);
        return $image_url;
    }

}