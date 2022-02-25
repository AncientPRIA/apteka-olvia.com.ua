<?php

namespace App\Processors;

use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image as Intervention;
use Illuminate\Support\Facades\File as File;

class ImageOptimizer
{
    public $base_path;

    public function __construct()
    {
        $this->base_path = public_path('uploads');
    }

    public function get_base_path(){
        return $this->base_path;
    }

    public function optimize_single_image($sizes = array()){

        setlocale(LC_ALL, 'ru_RU.utf8');
        $return = array();

        Intervention::configure(array('driver' => 'imagick'));

        //$original_relative_path = 'uploads/sputnix-img-2.jpg';
        //$original_relative_directory = File::dirname($original_relative_path);
        //$original_filename = File::name($original_relative_path); // Basename for name with ext
        //$original_ext = File::extension($original_relative_path);
        //$original_full_path = public_path($original_relative_path);

        $optimized_base_path = public_path('optimized/');
        //$optimized_full_path = $optimized_base_path.$original_relative_path;
        //$optimized_directory_full_path = File::dirname($optimized_full_path);


        //var_dump($optimized_full_path);
        //exit();

        $get_dimensions = $sizes['get_dimensions'] ?? false;


        foreach ($sizes as $key => $size){


            // if size have key relative_path use it
            if(isset($size['relative_path'])){

                $sizes[$key]['urlset'] = array();
                $original_relative_path = $size['relative_path'];
                //$original_relative_path = 'uploads/sputnix-img-2.jpg';
                $original_full_path = public_path($original_relative_path);
                // Skip if no file
                if(!file_exists($original_full_path) || is_dir($original_full_path)){
                    continue;
                }
                $original_relative_pathinfo = pathinfo($original_relative_path);
                $original_relative_directory = $original_relative_pathinfo['dirname']; //File::dirname($original_relative_path);
                $original_filename = $original_relative_pathinfo['filename']; //File::name($original_relative_path); // Basename for name with ext
                $original_ext = $original_relative_pathinfo['extension'];

                $optimized_full_path = $optimized_base_path.$original_relative_path;
                $optimized_full_pathinfo = pathinfo($optimized_full_path);
                $optimized_directory_full_path = $optimized_full_pathinfo['dirname'];
            }else{
                continue; // Skip if no relative path
            }
            //

            if(isset($size['q'])){
                $quality = $size['q'];
            }else{
                $quality = 85; // Default quality
            }

            if( ! File::isDirectory($optimized_directory_full_path) ) {
                File::makeDirectory($optimized_directory_full_path, 0775, true);
            }

            $filename_size = $original_filename.'_'.$size['width'];
            $filename_size = str_replace(' ', '%20', $filename_size);
            $optimized_full_filename_size = $optimized_directory_full_path.'/'.$filename_size;
//            if(isset($size['height']) AND $size['height'] !== null AND $size['height'] !== 0){
//                $optimized_full_filename_size .= 'x'.$size['height'];
//            }

            //dd($original_full_path);

            if(!isset($size['height'])){
                $size['height'] = 0;
            }

            try{

                if($get_dimensions || isset($size['watermark']['relative_path']) || $size['width'] === 'original' || isset($size['height'])){

                    $image = Intervention::make($original_full_path);

                    $sizes[$key]['original']['dimensions']['width'] = $image->width();
                    $sizes[$key]['original']['dimensions']['height'] = $image->height();
                    //$sizes[$key]['width'] = $size['width'] = $image->width();
                    //$sizes[$key]['height'] = $size['height'] = $image->height();
                }else{
                    $image = null;
                }

                if($size['width'] === 'original'){
                    $sizes[$key]['width'] = $size['width'] = $image->width();
                    $sizes[$key]['height'] = $size['height'] = $image->height();
                }


                // TODO: Deprecate?
                if($get_dimensions){
                    $sizes[$key]['original']['dimensions']['width'] = $image->width();
                    $sizes[$key]['original']['dimensions']['height'] = $image->height();
                    $info1x['dimensions']['width'] = $image->width();
                    $info1x['dimensions']['height'] = $image->height();
                    $info2x['dimensions']['width'] = $info1x['dimensions']['width'] * 2;
                    $info2x['dimensions']['height'] = $info1x['dimensions']['height'] * 2;
                    $info3x['dimensions']['width'] = $info1x['dimensions']['width'] * 3;
                    $info3x['dimensions']['height'] = $info1x['dimensions']['height'] * 3;
                }


                // Add watermark to image
                if(isset($size['watermark']['relative_path'])){

                    $watermark_full_path = public_path($size['watermark']['relative_path']);
                    $watermark = Intervention::make($watermark_full_path);

                    $percent = 15;
                    $watermark_target_width = round($sizes[$key]['original']['dimensions']['width'] * ($percent / 100), 0);

                    //$watermarkSize = round($img->width() * ((100 - $resizePercentage) / 100), 2); //watermark will be $resizePercentage less then the actual width of the image



                    // $watermark->resize($size['watermark']['width'] ?? null, $size['watermark']['height'] ?? null);
                    $watermark->resize($watermark_target_width, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->insert($watermark, $size['watermark']['position'] ?? 'bottom-right', $size['watermark']['x'] ?? 10, $size['watermark']['y'] ?? 10);
                    //dd($size['watermark'], $image, $image2);

                }


            }catch(\Exception $e){
                error_telegram("ImageOptimizer exception, check logs");
                Log::info("ImageOptimizer exception ".json_encode($size)
                    ." ".$e->getMessage()
                    ." ".$e->getLine()
//                    ." ".$e->getTraceAsString()
                    ." ".$e->getFile()
                );
                $image = null;
                $sizes[$key]['original']['dimensions']['width'] = 0;
                $sizes[$key]['original']['dimensions']['height'] = 0;
                $info1x['dimensions']['width'] = 0;//$image->width();
                $info1x['dimensions']['height'] = 0;//$image->height();
                $info2x['dimensions']['width'] = $info1x['dimensions']['width'] * 2;
                $info2x['dimensions']['height'] = $info1x['dimensions']['height'] * 2;
                $info3x['dimensions']['width'] = $info1x['dimensions']['width'] * 3;
                $info3x['dimensions']['height'] = $info1x['dimensions']['height'] * 3;
                continue;
            }


            switch ($original_ext){
                case 'jpg':
                case 'jpeg':

                    if(!File::exists($optimized_full_filename_size.'.'.'jpg') && File::exists($original_full_path)){
                        if($image !== null){
                            $image->backup();
                        }
                        $this->make_jpg($original_full_path, $optimized_full_filename_size.'.'.'jpg', $size['width'], $size['height'], $quality, $image);
                        if($image !== null){
                            $image->reset();
                        }

                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $this->make_jpg($original_full_path, $optimized_full_filename_size.'@2x.'.'jpg', $size['width'] * 2, $size['height'] * 2, $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }
                        }

                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $this->make_jpg($original_full_path, $optimized_full_filename_size.'@3x.'.'jpg', $size['width'] * 3, $size['height'] * 3, $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }
                        }
                    }

                    $sizes[$key]['urlset']['plain']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'.jpg');
                    $sizes[$key]['urlset']['plain']['mime'] = 'image/jpeg';
                    if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                        $sizes[$key]['urlset']['plain2x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@2x.jpg');
                        $sizes[$key]['urlset']['plain2x']['mime'] = 'image/jpeg';
                        $sizes[$key]['urlset']['plain3x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@3x.jpg');
                        $sizes[$key]['urlset']['plain3x']['mime'] = 'image/jpeg';
                    }
                    if($get_dimensions){
                        $sizes[$key]['urlset']['plain']['dimensions'] = $info1x['dimensions'];
                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $sizes[$key]['urlset']['plain2x']['dimensions'] = $info2x['dimensions'];
                            $sizes[$key]['urlset']['plain3x']['dimensions'] = $info3x['dimensions'];
                        }
                    }


                    if(!isset($size['no_webp']) || $size['no_webp'] !== true){
                        if(!File::exists($optimized_full_filename_size.'.'.'webp') && File::exists($original_full_path)){
                            if($image !== null){
                                $image->backup();
                            }
                            $this->make_webp($original_full_path, $optimized_full_filename_size.'.'.'webp', $size['width'], $size['height'], $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }

                            if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                                $this->make_webp($original_full_path, $optimized_full_filename_size.'@2x.'.'webp', $size['width'] * 2, $size['height'] * 2, $quality, $image);
                                if($image !== null){
                                    $image->reset();
                                }
                            }

                            if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                                $this->make_webp($original_full_path, $optimized_full_filename_size.'@3x.'.'webp', $size['width'] * 3, $size['height'] * 3, $quality, $image);
                                if($image !== null){
                                    $image->reset();
                                }

                            }
                        }

                        $sizes[$key]['urlset']['webp']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'.webp');
                        $sizes[$key]['urlset']['webp']['mime'] = 'image/webp';
                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $sizes[$key]['urlset']['webp2x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@2x.webp');
                            $sizes[$key]['urlset']['webp2x']['mime'] = 'image/webp';
                            $sizes[$key]['urlset']['webp3x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@3x.webp');
                            $sizes[$key]['urlset']['webp3x']['mime'] = 'image/webp';
                        }
                        if($get_dimensions){
                            $sizes[$key]['urlset']['webp']['dimensions'] = $info1x['dimensions'];
                            if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                                $sizes[$key]['urlset']['webp2x']['dimensions'] = $info2x['dimensions'];
                                $sizes[$key]['urlset']['webp3x']['dimensions'] = $info3x['dimensions'];
                            }
                        }
                    }
                    break;

                case 'png':
                    if(!File::exists($optimized_full_filename_size.'.'.'png') && File::exists($original_full_path)){
                        if($image !== null){
                            $image->backup();
                        }
                        $this->make_png($original_full_path, $optimized_full_filename_size.'.'.'png', $size['width'], $size['height'], $quality, $image);
                        if($image !== null){
                            $image->reset();
                        }

                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $this->make_png($original_full_path, $optimized_full_filename_size.'@2x.'.'png', $size['width'] * 2, $size['height'] * 2, $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }
                        }

                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $this->make_png($original_full_path, $optimized_full_filename_size.'@3x.'.'png', $size['width'] * 3, $size['height'] * 3, $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }

                        }
                    }

                    $sizes[$key]['urlset']['plain']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'.png');
                    $sizes[$key]['urlset']['plain']['mime'] = 'image/png';
                    if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                        $sizes[$key]['urlset']['plain2x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@2x.png');
                        $sizes[$key]['urlset']['plain2x']['mime'] = 'image/png';
                        $sizes[$key]['urlset']['plain3x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@3x.png');
                        $sizes[$key]['urlset']['plain3x']['mime'] = 'image/png';
                    }
                    if($get_dimensions){
                        $sizes[$key]['urlset']['plain']['dimensions'] = $info1x['dimensions'];
                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $sizes[$key]['urlset']['plain2x']['dimensions'] = $info2x['dimensions'];
                            $sizes[$key]['urlset']['plain3x']['dimensions'] = $info3x['dimensions'];
                        }
                    }



                    if(!isset($size['no_webp']) || $size['no_webp'] !== true){
                        if(!File::exists($optimized_full_filename_size.'.'.'webp') && File::exists($original_full_path)){
                            if($image !== null){
                                $image->backup();
                            }
                            $this->make_webp($original_full_path, $optimized_full_filename_size.'.'.'webp', $size['width'], $size['height'], $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }

                            if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                                $this->make_webp($original_full_path, $optimized_full_filename_size.'@2x.'.'webp', $size['width'] * 2, $size['height'] * 2, $quality, $image);
                                if($image !== null){
                                    $image->reset();
                                }
                            }

                            if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                                $this->make_webp($original_full_path, $optimized_full_filename_size.'@3x.'.'webp', $size['width'] * 3, $size['height'] * 3, $quality, $image);
                                if($image !== null){
                                    $image->reset();
                                }
                            }
                        }

                        $sizes[$key]['urlset']['webp']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'.webp');
                        $sizes[$key]['urlset']['webp']['mime'] = 'image/webp';
                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $sizes[$key]['urlset']['webp2x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@2x.webp');
                            $sizes[$key]['urlset']['webp2x']['mime'] = 'image/webp';
                            $sizes[$key]['urlset']['webp3x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@3x.webp');
                            $sizes[$key]['urlset']['webp3x']['mime'] = 'image/webp';
                        }
                        if($get_dimensions){
                            $sizes[$key]['urlset']['webp']['dimensions'] = $info1x['dimensions'];
                            if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                                $sizes[$key]['urlset']['webp2x']['dimensions'] = $info2x['dimensions'];
                                $sizes[$key]['urlset']['webp3x']['dimensions'] = $info3x['dimensions'];
                            }
                        }

                    }

                    break;
                case 'gif':

                    if(!File::exists($optimized_full_filename_size.'.'.'gif') && File::exists($original_full_path)){
                        if($image !== null){
                            $image->backup();
                        }
                        $this->make_gif($original_full_path, $optimized_full_filename_size.'.'.'gif', $size['width'], $size['height'], $quality, $image);
                        if($image !== null){
                            $image->reset();
                        }

                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $this->make_gif($original_full_path, $optimized_full_filename_size.'@2x.'.'gif', $size['width'] * 2, $size['height'] * 2, $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }
                        }

                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $this->make_gif($original_full_path, $optimized_full_filename_size.'@3x.'.'gif', $size['width'] * 3, $size['height'] * 3, $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }
                        }

                    }

                    $sizes[$key]['urlset']['plain']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'.gif');
                    $sizes[$key]['urlset']['plain']['mime'] = 'image/gif';
                    if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                        $sizes[$key]['urlset']['plain2x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@2x.gif');
                        $sizes[$key]['urlset']['plain2x']['mime'] = 'image/gif';
                        $sizes[$key]['urlset']['plain3x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@3x.gif');
                        $sizes[$key]['urlset']['plain3x']['mime'] = 'image/gif';
                    }
                    if($get_dimensions){
                        $sizes[$key]['urlset']['plain']['dimensions'] = $info1x['dimensions'];
                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $sizes[$key]['urlset']['plain2x']['dimensions'] = $info2x['dimensions'];
                            $sizes[$key]['urlset']['plain3x']['dimensions'] = $info3x['dimensions'];
                        }
                    }

                    if(!isset($size['no_webp']) || $size['no_webp'] !== true){
                        if(!File::exists($optimized_full_filename_size.'.'.'webp') && File::exists($original_full_path)){
                            if($image !== null){
                                $image->backup();
                            }
                            $this->make_webp($original_full_path, $optimized_full_filename_size.'.'.'webp', $size['width'], $size['height'], $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }

                            if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                                $this->make_webp($original_full_path, $optimized_full_filename_size.'@2x.'.'webp', $size['width'] * 2, $size['height'] * 2, $quality, $image);
                                if($image !== null){
                                    $image->reset();
                                }
                            }


                            if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                                $this->make_webp($original_full_path, $optimized_full_filename_size.'@3x.'.'webp', $size['width'] * 3, $size['height'] * 3, $quality, $image);
                                if($image !== null){
                                    $image->reset();
                                }
                            }

                        }

                        $sizes[$key]['urlset']['webp']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'.webp');
                        $sizes[$key]['urlset']['webp']['mime'] = 'image/webp';
                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $sizes[$key]['urlset']['webp2x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@2x.webp');
                            $sizes[$key]['urlset']['webp2x']['mime'] = 'image/webp';
                            $sizes[$key]['urlset']['webp3x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@3x.webp');
                            $sizes[$key]['urlset']['webp3x']['mime'] = 'image/webp';
                        }
                        if($get_dimensions){
                            $sizes[$key]['urlset']['webp']['dimensions'] = $info1x['dimensions'];
                            if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                                $sizes[$key]['urlset']['webp2x']['dimensions'] = $info2x['dimensions'];
                                $sizes[$key]['urlset']['webp3x']['dimensions'] = $info3x['dimensions'];
                            }
                        }

                    }
                    break;
                case 'webp':
                    if(!File::exists($optimized_full_filename_size.'.'.'webp') && File::exists($original_full_path)){
                        if($image !== null){
                            $image->backup();
                        }

                        $this->make_webp($original_full_path, $optimized_full_filename_size.'.'.'webp', $size['width'], $size['height'], $quality, $image);
                        if($image !== null){
                            $image->reset();
                        }

                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $this->make_webp($original_full_path, $optimized_full_filename_size.'@2x.'.'webp', $size['width'] * 2, $size['height'] * 2,  $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }
                        }

                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $this->make_webp($original_full_path, $optimized_full_filename_size.'@3x.'.'webp', $size['width'] * 3, $size['height'] * 3, $quality, $image);
                            if($image !== null){
                                $image->reset();
                            }
                        }

                    }

                    $sizes[$key]['urlset']['webp']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'.webp');
                    $sizes[$key]['urlset']['webp']['mime'] = 'image/webp';
                    if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                        $sizes[$key]['urlset']['webp2x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@2x.webp');
                        $sizes[$key]['urlset']['webp2x']['mime'] = 'image/webp';
                        $sizes[$key]['urlset']['webp3x']['url'] = \URL::asset('optimized/'.$original_relative_directory.'/'.$filename_size.'@3x.webp');
                        $sizes[$key]['urlset']['webp3x']['mime'] = 'image/webp';
                    }
                    if($get_dimensions){
                        $sizes[$key]['urlset']['webp']['dimensions'] = $info1x['dimensions'];
                        if(!isset($size['no_retina']) || $size['no_retina'] !== true){
                            $sizes[$key]['urlset']['webp2x']['dimensions'] = $info2x['dimensions'];
                            $sizes[$key]['urlset']['webp3x']['dimensions'] = $info3x['dimensions'];
                        }
                    }

                    break;
                default:
                    // unrecognized format
                    break;
            }
        }

//        if($sizes['1']['relative_path'] === "uploads/posts/IMG_4642.jpg"){
//            $processUser = posix_getpwuid(posix_geteuid());
//            dd($processUser);
//            dd($sizes);
//        }

        return $sizes;

    }

    // Make webp image and save it to disk
    private function make_webp($original_full_path, $optimized_full_path, $width = null, $height = null, $quality = 65, $image = null){
        $info = [];
        try{
            if($height === 0){
                $height = null;
            }
            if($image === null){
                $image = Intervention::make($original_full_path);
            }
            //$core = $image->getCore();
            //$core->stripImage();
            //$image->setCore($core);

            $image->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                //$constraint->upsize();
            })
                ->encode('webp', $quality)
                ->save($optimized_full_path, $quality);

            //$info['dimensions']['width'] = $image->width();
            //$info['dimensions']['height'] = $image->height();

        }catch (\Exception $e){
        }
        return $info;
    }

    // Make jpg image and save it to disk
    private function make_jpg($original_full_path, $optimized_full_path, $width = null, $height = null, $quality = 65, $image = null){
        $info = [];
        try{
            if($image === null){
                $image = Intervention::make($original_full_path);
            }

            $core = $image->getCore();
            $core->stripImage();
            $image->setCore($core);

            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                //$constraint->upsize();
            })
                ->encode('jpg', $quality)
                ->save($optimized_full_path, $quality);

            //$info['dimensions']['width'] = $image->width();
            //$info['dimensions']['height'] = $image->height();

        }catch (\Exception $e){

        }

        return $info;
    }

    // Make png image and save it to disk
    private function make_png($original_full_path, $optimized_full_path, $width = null, $height = null, $quality = 65, $image = null){
        $info = [];

        try{
            if($image === null){
                $image = Intervention::make($original_full_path);
            }

            $core = $image->getCore();
            $core->stripImage();
            $image->setCore($core);

            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                //$constraint->upsize();
            })
                ->encode('png', $quality)
                ->save($optimized_full_path, $quality);

            //$info['dimensions']['width'] = $image->width();
            //$info['dimensions']['height'] = $image->height();

        }catch (\Exception $e){

        }
        return $info;
    }

    // Make gif image and save it to disk
    private function make_gif($original_full_path, $optimized_full_path, $width = null, $height = null, $quality = 65, $image = null){
        $info = [];
        try{
            if($image === null){
                $image = Intervention::make($original_full_path);
            }

            $core = $image->getCore();
            $core->stripImage();
            $image->setCore($core);

            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                //$constraint->upsize();
            })
                ->encode('gif', $quality)
                ->save($optimized_full_path, $quality);


            //$info['dimensions']['width'] = $image->width();
            //$info['dimensions']['height'] = $image->height();

        }catch (\Exception $e){

        }

        return $info;
    }



}