<?php
/*
 * . Create App in VK (standalone)
 * . Get $app_id (ID приложения), $app_secure_key (Защищённый ключ	) and $app_secret_access_key (Сервисный ключ доступа) in app settings
 * . Change $scope and run get_access_key_url to get $access_key.
 * . Fast start in the end of this file
 *
 */

class VK_SDK
{
    private $app_id = '';
    private $app_secure_key = '';
    private $secret_access_key = '';
    private $access_key = '';
    private $owner_id = '';
    private $access_token = '';
    private $api_base = 'https://api.vk.com/method/';
    //private $api_version = '5.92';
    private $api_version = '5.103';
    // Use secret access key instead of access key
    private $use_secret_access_key = false;
    public $tmp_folder;

    // default scope (all)
    private $scope =
        'notify'.
        ',photos'.
        //',messages'.
        ',audio'.
        ',video'.
        ',pages'.
        ',docs'.
        ',status'.
        ',questions'.
        ',offers'.
        ',wall'.
        ',groups'.
        ',notifications'.
        ',stats'.
        ',ads'.
        ',offline'.
        ',market';

    public function __construct()
    {
    }


    // Set app id
    public function set_app_id($app_id){
        $this->app_id = $app_id;
    }

    // Set app secure key
    public function set_app_secure_key($app_secure_key){
        $this->app_secure_key = $app_secure_key;
    }

    // Set app secret access key
    public function set_app_secret_access_key($app_secret_access_key){
        $this->secret_access_key = $app_secret_access_key;
    }

    // Set access token
    public function set_access_token($token){
        $this->access_token = $token;
    }

    // Set access key
    public function set_access_key($key){
        $this->access_key = $key;
    }

    // Set group_id
    public function set_owner_id($owner_id){
        $this->owner_id = $owner_id;
    }

    // Generate url for access key. Paste it in adress bar and you get access key.
    public function get_access_key_url(){
        $url = 'https://oauth.vk.com/authorize?client_id='.$this->app_id.'&scope='.urlencode($this->scope).'&redirect_uri=http://api.vk.com/blank.html&display=page&response_type=token';
        return $url;
    }



    public function make_request($method, $args_array){
        $use_post = true;

        if(!isset($args_array['v'])){
            $args_array['v'] = $this->api_version;
        }
        if(!isset($args_array['owner_id'])){
            $args_array['owner_id'] = $this->owner_id;
        }
        if(!isset($args_array['access_token'])){
            if($this->use_secret_access_key){
                $args_array['access_token'] = $this->secret_access_key;
            }else{
                $args_array['access_token'] = $this->access_key;
            }
        }

        if($use_post){
            $params = http_build_query($args_array);
            $url = $this->api_base.$method;

            //$url = "https://api.vk.com/method/photos.getMarketUploadServer";
            //dd($url, $params);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,3);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5); //timeout in seconds
            $result = curl_exec($curl);
            //dd($result);

            curl_close($curl);
        }else{
            $params = http_build_query($args_array);
            $url = $this->api_base.$method.'?'.$params;

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            curl_close($curl);
        }


        $result = json_decode($result, true);

        return $result;
    }

    // Get upload url for market
    public function get_upload_url_market($group_id, $is_main_photo = false){
        $method = 'photos.getMarketUploadServer';
        $args = array(
            'group_id'        => $group_id,
            //'crop_x'        => null,
            //'crop_y'        => null,
            //'crop_width'    => null,
        );

        if($is_main_photo){
            $args['main_photo'] = 1;
        }else{
            $args['main_photo'] = 0;
        }
        $result = $this->make_request($method, $args);
        if(isset($result['response']['upload_url'])){
            return $result['response']['upload_url'];
        }else{
            return false;
        }
    }

    // Upload files to vk
    // $filepathes = array('full_filepath1', 'full_filepath2'...)
    public function upload_files_market($filepathes, $group_id, $is_main_photo = false){
        if(is_array($filepathes)){
            $urls_count = count($filepathes);
        }else{
            $urls_count = -1;
        }

        if($urls_count > 5){
            //return 'ERROR: max 5 urls, '.$urls_count.' provided';
            throw new Exception("VK allows 5 images in gallery. $urls_count given");
        }

        $upload_url = $this->get_upload_url_market($group_id, $is_main_photo);
        if($upload_url === false){
            throw new Exception("get_upload_url_market failed " . $upload_url);
        }

        $post = array();
        if($urls_count !== -1){
            for($i = 0; $i < $urls_count; $i++){
                $file = new CURLFile($filepathes[$i]);
                $post['file'.($i+1)] = $file;
            }
        }else{
            $file = new CURLFile($filepathes);
            $post['file0'] = $file;
        }


        //dd($post);
        $curl = curl_init( $upload_url );
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"] );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,3);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5); //timeout in seconds
        $result = curl_exec( $curl );
        //dd($result);
        $result = json_decode($result, true);
        curl_close( $curl );
        //

        if(isset($result["error"])){
            throw new Exception("image upload error " . $result["error"]);
        }

        $method = 'photos.saveMarketPhoto';
        $args_array = [
            'server' => $result['server'],
            'hash' => $result['hash'],

            'photo' => stripslashes($result['photo']), // photos_list for not market
        ];
        if($is_main_photo){
            $args_array['crop_data'] = $result['crop_data'];
            $args_array['crop_hash'] = $result['crop_hash'];
        }
        if(!isset($args_array['group_id'])){
            $args_array['group_id'] = $group_id;
        }
        if(!isset($args_array['v'])){
            $args_array['v'] = $this->api_version;
        }
        if(!isset($args_array['owner_id'])){
            $args_array['owner_id'] = $this->owner_id;
        }
        if(!isset($args_array['access_token'])){
            if($this->use_secret_access_key){
                $args_array['access_token'] = $this->secret_access_key;
            }else{
                $args_array['access_token'] = $this->access_key;
            }
        }

        //$params = http_build_query($args_array);
        //$url = $this->api_base.$method.'?'.$params;
        $result = $this->make_request($method, $args_array);

        return $result;
    }

    // Add product to market
    /*
    In
    $group_id       173332978
    $poster_path    array('full_path')
    $gallery_ids    array(1,2,3)
    $prepared       array('title'=>'', 'description'=>'', 'url'=>'', 'price'=>12500)

    Out
    $add_result     array['response']['market_item_id'] (int)
    */
    public function add_to_market($group_id, $poster_path, $gallery, $prepared, $image_bg_fit){
        $max_additional_images = 4;

        $tmp = $this->fit_image($image_bg_fit, $poster_path, 400, 400);
        if($tmp !== false){
            $poster_path = $tmp;
        }else{
            throw new Exception('error in poster fit_image');
        }

        $upload_result = $this->upload_files_market($poster_path, $group_id, 1);
        if(!isset($upload_result['response'][0]['id'])){
            throw new Exception("Can't find poster image id from vk ".$poster_path." ".json_encode($upload_result));
        }
        $prepared['main_photo_id'] = $upload_result['response'][0]['id'];
        $prepared['photo_ids'] = '';

        $count_additional_images = 0;
        foreach ($gallery as $gallery_image){
            if($max_additional_images !== -1 AND $count_additional_images >= $max_additional_images){
                break;
            }
            if($poster_path === $gallery_image){
                continue;
            }

            $tmp = $this->fit_image($image_bg_fit, $gallery_image, 400, 400);
            if($tmp !== false){
                $gallery_image_path = $tmp;
            }else{
                throw new Exception("error in gallery fit_image");
            }

            $upload_result = $this->upload_files_market(array($gallery_image_path), $group_id, 0);
            if(!isset($upload_result['response'][0]['id'])){
                $upload_result = $this->upload_files_market(array($gallery_image_path), $group_id, 0);
                if(!isset($upload_result['response'][0]['id'])){
                    throw new Exception("Can't find gallery image id from vk ".$gallery_image_path);
                }
            }
            $prepared['photo_ids'] .= $upload_result['response'][0]['id'].',';

            $count_additional_images++;
        }
        $prepared['photo_ids'] = rtrim($prepared['photo_ids'], ',');

        $method = 'market.add';
        $args = array(
            'name'          => $prepared['title'],
            'description'   => $prepared['description'],
            'category_id'   => $prepared['category_id'],
            'price'         => $prepared['price'],
            'deleted'       => 0,
            'main_photo_id' => $prepared['main_photo_id'],
            'photo_ids'     => $prepared['photo_ids'],
            'url'           => $prepared['url'],

        );
        $add_result = $this->make_request($method, $args);
        return $add_result;
    }

    public function add_to_album($market_item_id, $album_id){
        $method = 'market.addToAlbum';
        $args = array(
            'item_id' => $market_item_id,
            'album_ids' => $album_id
        );
        $add_result = $this->make_request($method, $args);
        return $add_result;
    }

    // -----------------------

    public function get_wall_posts($offset){
        $method = "wall.get";
        $args = array(
            'owner_id'  => $this->owner_id,
            //'album_id' =>
            'offset'    => $offset,
            'count'     => 100,
        );
        $vk_posts = $this->make_request($method, $args);
        return $vk_posts;
    }
    public function get_by_id($vk_id){
        $method = "wall.getById";
        $args = array(
            'posts'  => $this->owner_id.'_'.$vk_id,
        );
        $vk_posts = $this->make_request($method, $args);
        return $vk_posts;
    }

    // -----------------------

    public function get_upload_url_wall($group_id){
        ltrim($group_id, "-");
        $method = 'photos.getWallUploadServer';
        $args = array(
            'group_id'        => $group_id,
            //'crop_x'        => null,
            //'crop_y'        => null,
            //'crop_width'    => null,
        );

        $result = $this->make_request($method, $args);
        if(isset($result['response']['upload_url'])){
            return $result;
        }else{
            return false;
        }
    }

    public function upload_file_wall($group_id, $filepath){

        $result = $this->get_upload_url_wall($group_id);
        var_dump($result);
        $upload_url = $result['response']['upload_url'];
        $user_id = $result['response']['user_id'];
        if($upload_url === false){
            return "ERROR: upload_url false";
        }

        $post = array();
        $file = new CURLFile($filepath);
        $post['file1'] = $file;

        $curl = curl_init( $upload_url );
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: multipart/form-data"] );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false );
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec( $curl );
        $result = json_decode($result, true);
        curl_close( $curl );

        $method = 'photos.saveWallPhoto';
        $args_array = [
            //'user_id' => $user_id,
            'group_id' => ltrim($group_id, '-'),
            'server' => $result['server'],
            'hash' => $result['hash'],
            'photo' => stripslashes($result['photo']), // photos_list for not market
        ];
        if(!isset($args_array['v'])){
            $args_array['v'] = $this->api_version;
        }
        if(!isset($args_array['access_token'])){
            if($this->use_secret_access_key){
                $args_array['access_token'] = $this->secret_access_key;
            }else{
                $args_array['access_token'] = $this->access_key;
            }
        }

        //$params = http_build_query($args_array);
        //$url = $this->api_base.$method.'?'.$params;

        $result = $this->make_request($method, $args_array);

        $biggest_image_url = $this->get_biggest_image_from_sizes($result['response'][0]['sizes']);
        $result['image_url'] = $biggest_image_url;

        return $result;
    }
    public function post_to_wall($group_id, $text, $filepath){
        $upload_result = $this->upload_file_wall($group_id, $filepath);
        if(!isset($upload_result['response'][0]['id'])){
            $upload_result = $this->upload_file_wall($group_id, $filepath);
            if(!isset($upload_result['response'][0]['id'])){
                echo "UPLOAD_TO_VK: No vk image id".PHP_EOL;
                return false;
            }
        }

        $img_url = $upload_result['image_url'];

        //$text .= "\n".$img_url;

        $attach_owner_id = $upload_result['response'][0]['owner_id']; //$this->owner_id;
        $method = 'wall.post';
        $args = array(
            'owner_id'  => $this->owner_id,
            'friends_only' => 0,
            'from_group' => 1,
            'message' => $text,
            'attachments' => 'photo'.$attach_owner_id.'_'.$upload_result['response'][0]['id'],

        );
        $vk_post = $this->make_request($method, $args);
        if(isset($vk_post['response']['post_id'])){
            return $vk_post['response']['post_id'];
        }else{
            return false;
        }
    }


    /* -------------- HELPERS --------------- */
    public function api_test(){
        $method = 'market.get';
        $args = array(
            'owner_id'  => $this->owner_id,
        );
        $test_result = $this->make_request($method, $args);
        return $test_result;

    }

    public function get_image_from_vk_attachment($vk_attachment, $width_max = -1, $height_max = -1){
        if($vk_attachment['type'] === 'photo'){
            $current_width = 0;
            $current_height = 0;
            $current_url = '';
            foreach ($vk_attachment['photo']['sizes'] as $vk_photo_size){

                if(
                    $current_url === ''
                    OR
                    (
                        ($vk_photo_size['width'] > $current_width OR $vk_photo_size['height'] > $current_height)
                        AND (
                            ($vk_photo_size['width'] <= $width_max) OR ($width_max === -1)
                            AND
                            ($vk_photo_size['height'] <= $width_max) OR ($height_max === -1)
                        )
                    )

                ){
                    $current_url = $vk_photo_size['url'];
                }

            }

            if($current_url === ''){
                return false;
            }else{
                return $current_url;
            }
        }else{
            return false;
        }
    }

    public function get_biggest_image_from_sizes($sizes){
        $biggest_width = 0;
        $biggest_image_url = '';
        foreach ($sizes as $vk_img_arr){
            $width = $vk_img_arr['width'];
            if($width > $biggest_width){
                $biggest_width = $width;
                $biggest_image_url = $vk_img_arr['url'];
            }
        }
        return $biggest_image_url;
    }

    public function fit_image($image_container, $image_layer, $desired_width = 400, $desired_height = 400){
        global $custom_vars;
        $ready_image_folder = public_path('tmp');
        if(!file_exists($ready_image_folder)){
            mkdir($ready_image_folder, 0755, true);
        }

        $dimensions = getimagesize($image_layer);

        $info_image_bg = pathinfo($image_container);


        $info = pathinfo($image_layer);
        $filename = $info['filename'].'.png';
        //$ext = $info['extension'];

        if($info_image_bg['extension'] === 'png'){
            $obj_image_background = imagecreatefrompng($image_container);
        }elseif($info_image_bg['extension'] === 'jpg'){
            $obj_image_background = imagecreatefromjpeg($image_container);
        }

        $obj_image_background_x = imagesx($obj_image_background);
        $obj_image_background_y = imagesy($obj_image_background);

        if($info['extension'] === 'png'){
            $obj_image_property = imagecreatefrompng($image_layer);
        }elseif($info['extension'] === 'jpg'){
            $obj_image_property = imagecreatefromjpeg($image_layer);
        }
        $obj_image_property_x = imagesx($obj_image_property);
        $obj_image_property_y = imagesy($obj_image_property);

        if($dimensions[0] < $desired_width AND $dimensions[1] < $desired_height){
            //echo 'POSTER WIDTH AND HEIGHT < ".PHP_EOL;

            $dst_x = round(($obj_image_background_x-$obj_image_property_x)/2, 0, PHP_ROUND_HALF_DOWN);
            $dst_y = round(($obj_image_background_y-$obj_image_property_y)/2, 0, PHP_ROUND_HALF_DOWN);
            imagecopyresampled (
                $obj_image_background, $obj_image_property,
                $dst_x, $dst_y, 0, 0,
                $obj_image_property_x, $obj_image_property_y,
                $obj_image_property_x, $obj_image_property_y
            );
            if(imagepng($obj_image_background, $ready_image_folder.'/'.$filename)){
                return $ready_image_folder.'/'.$filename;
            }else{
                return false;
            }

        }elseif($dimensions[0] < $desired_width){
            //echo 'POSTER WIDTH < '.PHP_EOL;

            $desired_height = $obj_image_background_y;
            $scaling_factor = $desired_height / $obj_image_property_y;

            // Inserted image desired width and height
            $dst_w = round(($obj_image_property_x * $scaling_factor), 0, PHP_ROUND_HALF_DOWN);
            $dst_h = round(($obj_image_property_y * $scaling_factor), 0, PHP_ROUND_HALF_DOWN);

            // Start point
            $dst_x = round(($obj_image_background_x - $dst_w) / 2, 0, PHP_ROUND_HALF_DOWN);
            $dst_y = 0;

            imagecopyresampled (
                $obj_image_background, $obj_image_property,
                $dst_x, $dst_y,
                0, 0,
                $dst_w, $dst_h,
                $obj_image_property_x, $obj_image_property_y
            );
            if(imagepng($obj_image_background, $ready_image_folder.'/'.$filename)){
                return $ready_image_folder.'/'.$filename;
            }else{
                return false;
            }

        }elseif($dimensions[1] < $desired_height){
            //echo 'POSTER HEIGHT < '.PHP_EOL;

            $desired_width = $obj_image_background_y;
            $scaling_factor = $desired_width / $obj_image_property_x;

            // Inserted image desired width and height
            $dst_w = round(($obj_image_property_x * $scaling_factor), 0, PHP_ROUND_HALF_DOWN);
            $dst_h = round(($obj_image_property_y * $scaling_factor), 0, PHP_ROUND_HALF_DOWN);

            // Start point
            $dst_x = 0;
            $dst_y = round(($obj_image_background_y - $dst_h) / 2, 0, PHP_ROUND_HALF_DOWN);;

            imagecopyresampled (
                $obj_image_background, $obj_image_property,
                $dst_x, $dst_y,
                0, 0,
                $dst_w, $dst_h,
                $obj_image_property_x, $obj_image_property_y
            );
            if(imagepng($obj_image_background, $ready_image_folder.'/'.$filename)){
                return $ready_image_folder.'/'.$filename;
            }else{
                return false;
            }

        }else{
            return $image_layer;
        }

    }


}



//FAST START
/*
ini_set('display_errors', 1);
echo '<pre>'.PHP_EOL;
global $custom_vars;
include $custom_vars['php_dir'].'/libraries/vk/VK_SDK.php';

$limit = -1;
$count = 0;

$app_id = 6990275;
$app_secure_key = 'Qa0kPRIOioCeOscPFcNB';
$app_secret_access_key = 'b42f4f80b42f4f80b42f4f8052b445e643bb42fb42f4f80e8cd16970126ff28180bc627';
$owner_id = -173332978;
$group_id = 173332978;

$vk_api = new VK_SDK();
$vk_api->set_app_id($app_id);
$vk_api->set_owner_id($owner_id);
$vk_api->set_app_secure_key($app_secure_key);
$vk_api->set_app_secret_access_key($app_secret_access_key);
$vk_api->set_access_token('a9d02344617c036f77feb56338350e2e54fdc7b388eac743005e921f5b4340a932a973a5dea270cd17af5');

//$access_key_url = $vk_api->get_access_key_url();

$vk_api->set_access_key('7f7ac8d982815b6a1106f822ed81f1ff51aba8aa5ea510ad76ff2c0bfbd5732ea56da9859b1bbe79de3f9');

$test_result = $vk_api->api_test();
*/