<?php

/*
. get develop rights at https://ok.ru/devaccess
. create application
. set app type External, Приложение для групп
. set fields:
    Ссылка на приложение = base_url
    Список разрешённых redirect_uri = base_url
    Клиентская OAuth авторизация = true
. apply for needed rights (https://apiok.ru/ext/oauth/permissions)
. authorize https://connect.ok.ru/oauth/authorize?client_id={clientId}&scope={scope}&response_type={{response_type}}&redirect_uri={redirectUri}&layout={layout}&state={state}

*/


class OK_SDK{

    public $api_base = 'https://api.ok.ru/fb.do?';
    public $api_base_post = 'https://api.ok.ru/api';
    public $app_id = '';
    public $app_public_key = '';
    public $app_secret_key = '';
    public $access_token = '';
    public $secret_session_key = '';

    private $request_secret_key = '';


    function __construct($app_id, $app_public_key, $app_secret_key)
    {
        $this->app_id = $app_id;
        $this->app_public_key = $app_public_key;
        $this->app_secret_key = $app_secret_key;
    }

    public function set_access_token($access_token){
        $this->access_token = $access_token;
    }

    public function set_secret_session_key($secret_session_key){
        $this->secret_session_key = $secret_session_key;
    }

    public function generate_auth_url($user_id, $scope){
        $url = 'https://connect.ok.ru/oauth/authorize?client_id='.$user_id.'&scope={scope}&response_type={{response_type}}&redirect_uri={redirectUri}&layout={layout}&state={state}';
    }

    private function generate_request_secret_key($args_array){
        $tmp_secret_key = strtolower(md5($this->access_token.$this->app_secret_key));

        $requestStr = "";
        foreach($args_array as $key=>$value){
            $requestStr .= $key . "=" . $value;
        }
        $requestStr .= $tmp_secret_key;

        $this->request_secret_key = strtolower(md5($requestStr));
    }


    public function make_request($method, $args_array){
        $args_array['application_key'] = $this->app_public_key;
        if(!isset($args_array['format'])){
            $args_array['format'] = 'json';
        }
        $args_array['method'] = $method;

        ksort($args_array);
        $this->generate_request_secret_key($args_array);

        $args_array['sig'] = $this->request_secret_key;
        $args_array['access_token'] = $this->access_token;

        $request_str = '';
        foreach($args_array as $key=>$value){
            $request_str .= $key . "=" . urlencode($value) . "&";
        }
        $request_str = substr($request_str, 0, -1);
        //var_dump('REQUEST STRING = '.$request_str);

        $curl = curl_init($this->api_base . $request_str);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($curl);
        curl_close($curl);
        return $json;
    }

    public function make_request_post($method, $args_array){
        $args_array['application_key'] = $this->app_public_key;
        if(!isset($args_array['format'])){
            $args_array['format'] = 'json';
        }
        $args_array['method'] = $method;

        ksort($args_array);
        $this->generate_request_secret_key($args_array);

        $args_array['sig'] = $this->request_secret_key;
        $args_array['access_token'] = $this->access_token;

        $request_str = '';

        $request_str .= 'method' . "=" . urlencode($args_array['method'])."&";
        //$request_str .= 'application_key' . "=" . urlencode($args_array['application_key']). "&";
        //$request_str .= 'sig' . "=" . urlencode($args_array['sig']);// . "&";
        //$request_str .= 'application_key' . "=" . urlencode($args_array['application_key']) . "&";


        $request_str = substr($request_str, 0, -1);
        //$request_str = 'method='.$method;
        //var_dump('REQUEST STRING = '.$request_str);

        $method_for_post = '';
        $method_segments = explode('.', $method);
        foreach ($method_segments as $method_segment){
            $method_for_post .= '/'.$method_segment;
        }

        //var_dump($this->api_base.$request_str);
        //var_dump($args_array);
        $curl = curl_init($this->api_base_post.$method_for_post);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($args_array));
        $json = curl_exec($curl);
        curl_close($curl);
        return $json;
    }



    /*
     * $images['file'] - path with filename and ext
     * $images['type'] - mimetype
     * $images['name'] - filename with ext
     * */
    public function upload_images($upload_url, $images){

        $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
        //$postfields = array("filedata" => "@$filedata", "filename" => $filename);
        $postfields = array();
        $count = 0;
        foreach ($images as $image){
            $pathinfo = pathinfo($image);
            switch ($pathinfo['extension']){
                case 'jpg':
                case 'jpeg':
                    $mime = 'image/jpeg';
                    break;
                case 'png':
                    $mime = 'image/png';
                    break;
                case 'gif':
                    $mime = 'image/gif';
                    break;
            }

            $cfile = new CURLFile($image, $mime, $pathinfo['filename']);
            $postfields['file['.$count.']'] = $cfile;
            $count++;
        }

        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $upload_url,
            //CURLOPT_HEADER => true,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_RETURNTRANSFER => true
        ); // cURL options
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        //var_dump($result);
        return $result;

    }

    public function image_path($attachment_id, $size = 'thumbnail') {
        $file = get_attached_file($attachment_id, true);
        if (empty($size) || $size === 'full') {
            // for the original size get_attached_file is fine
            return realpath($file);
        }
        if (! wp_attachment_is_image($attachment_id) ) {
            return false; // the id is not referring to a media
        }
        $info = image_get_intermediate_size($attachment_id, $size);
        if (!is_array($info) || ! isset($info['file'])) {
            return false; // probably a bad size argument
        }

        return realpath(str_replace(wp_basename($file), $info['file'], $file));
    }

    public function shorten_text($text, $length = 200){
        $return = '';
        $excerpt = $text;
        $length++;

        if ( mb_strlen( $excerpt ) > $length ) {
            $subex = mb_substr( $excerpt, 0, $length - 5 );
            $exwords = explode( ' ', $subex );
            $excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
            if ( $excut < 0 ) {
                $return = mb_substr( $subex, 0, $excut );
            } else {
                $return = $subex;
            }
            $return .= '...';
        } else {
            $return = $excerpt;
        }
        return $return;
    }
}