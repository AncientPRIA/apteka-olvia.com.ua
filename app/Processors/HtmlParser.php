<?php

// https://symfony.com/doc/current/components/dom_crawler.html

namespace App\Processors;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

class HtmlParser
{
    public $base_url = null;

    // Send Post request by curl
    public function get_post_content($url, $post_params){
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        //curl_setopt($curl, CURLOPT_HTTPHEADER, '');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_params);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($curl);
    }

    public function get_get_content($url){
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($curl);
    }

    public function get_get_content_advanced($url, $params = array()){
        $cookie_file = base_path('data/curl_cookie');
        $curl = curl_init($url);
        //$cookies = 'subdomainPARTNER=NSINT; JSESSIONID=CB3FEB3AC72AD61A80BFED91D3FD96CA; www-20480=MHFBNLFDFAAA; campaignPos=5; www-47873=MGFBNLFDFAAA; __utma=1.993399624.1370027094.1370040145.1370082133.5; __utmc=1; __utmz=1.1370027094.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); BCSessionID=5dc05787-c2c8-43e1-9abe-93989970b087; BCPermissionLevel=PERSONAL; __utmb=1.1.10.1370082133';
        $cookies = 'mindboxDeviceUUID=2875d367-4bb8-45a6-b52d-75d37cad6727';
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
            'Accept-Encoding: gzip, deflate, br',
            'Accept-Language: en,ru;q=0.9,ja;q=0.8,en-US;q=0.7',
            'Cache-Control: max-age=0',
            'Connection: keep-alive',
            'Cookie: BITRIX_SM_CITY_DEFINED=Y; PREVIOUS_STORE_ID=6; rerf=AAAAAF5Wh5UJjrGNAxGlAg==; ipp_uid2=ZIsZZbGaAl63BtTJ/BUbpLZncVOSFQTZAQIn7xQ==; ipp_uid1=1582628799522; ipp_uid=1582628799522/uuoFb1LjWRkqOHHr/ymEnpWvfB5rtnHPumgFxuw==; _ym_d=1582625196; _ym_uid=1582625196932646078; mindboxDeviceUUID=2875d367-4bb8-45a6-b52d-75d37cad6727; directCrm-session=%7B%22deviceGuid%22%3A%222875d367-4bb8-45a6-b52d-75d37cad6727%22%7D; _ga=GA1.2.1118547428.1582625197; _gid=GA1.2.1051117338.1582625197; BX_USER_ID=8b5972651673d15cd24e448dccdeaddb; _ym_isad=1; ipp_sign=a4556b271136ad8f890c9e5108c04480_506520859_8a57a3cf21849917af90423221a4f2bb; FULLSCREEN_BANNER_DISPLAYED=Y; ipp_key=v1582729109202/v3394bd400b5e53a13cfc65163aeca6afa04ab3/swWbMa/eGaPkJrRujihYdA==; _ym_visorc_22004554=w; _ym_visorc_22004554=w; age_confirmed=1; PHPSESSID=901AA2M5VL4gs7UcslaWPk8ZozANWNt5; _gat=1',
            'Host: www.eapteka.ru',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.122 Safari/537.36',
        ];
        $headers[] = 'Cookie: ' . $cookies;

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($curl, CURLOPT_VERBOSE, true);
        $result =  curl_exec($curl);
        return $result;
    }

    // Returns array of doms (like array of posts)
    public function get_doms($html, $path_to){
        $crawler = new Crawler($html);

        return $crawler->filter($path_to)->each(function (Crawler $node, $i) {
            return $node->html();
        });
    }

    public function get_urls($html, $path_to_a){
        // Create new instance for parser.
        $crawler = new Crawler();
        $crawler->addHtmlContent($html, 'UTF-8');

        // Get <a>.
        $elements = $crawler->filter($path_to_a)->each(function (Crawler $node, $i) {
            return $node->link()->getUri();

        });;
        return $elements;
    }

    public function get_parts($html, $parts_arr, $base_url = null){
        foreach ($parts_arr as $key => $item){
            $crawler = new Crawler(null, $base_url);
            $crawler->addHtmlContent($html, 'UTF-8');
            $path = $item['path'];
            $options = $item['options'] ?? [];
            $crawler = $crawler->filter($path);
            if($crawler->count()){
                $node_name = $crawler->nodeName();
                switch ($node_name){
                    case 'img':
                        $parts_arr[$key] = $crawler->image()->getUri();
                        break;
                    case 'a':
                        $parts_arr[$key] = $crawler->each(function (Crawler $node, $i) {
                            return [
                                'url' => $node->link()->getUri(),
                                'text' =>$node->text(),
                            ];
                        });
                        break;
                    default:
                        // Get attribute
                        if(isset($options['get_attributes'])){
                            $attributes = $options['get_attributes'];
                            $parts_arr[$key] = $crawler->extract([$attributes]);

                        }else{
                            // Get array of htmls
                            if(isset($options['many']) && $options['many'] === true){
                                $parts_arr[$key] = $crawler->each(function (Crawler $node, $i) use ($options) {
                                    if(isset($options['text']) AND $options['text'] === true){
                                        return $node->text();
                                    }else{
                                        return $node->html();
                                    }
                                });

                            }else{
                                // Get single html
                                if(isset($options['text']) AND $options['text'] === true){
                                    $parts_arr[$key] = $crawler->text();
                                }else{
                                    $parts_arr[$key] = $crawler->html();
                                }

                            }

                        }

                        break;
                }
            }else{
                $parts_arr[$key] = null;
            }
        }
        return $parts_arr;
    }

    // TODO: Automatic streaming
    // Returns filename with ext or false
    public function download_image($url, $folder, $file_name_wo_ext){

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

}