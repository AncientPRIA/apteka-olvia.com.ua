<?php

namespace App\Http\Controllers;

use App\Helpers\Microdata;
use App\Models\City;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ShopLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

use App\Models\TranslatableString;
use App\Models\Post;
use App\Models\Metadata;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use TCG\Voyager\Models\Translation;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Telegram\Bot\Laravel\Facades\Telegram;

class ContactController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index()
    {

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        // Meta
        $meta_key = 'contacts';
        $metadata = get_meta($meta_key);
        if($metadata['title'] === ""){
            $metadata['title'] = "Контакты - Сеть аптек “Ольвия”";
        }
        // Meta END

        // Microdata
        $microdata_key = 'structured_'.$meta_key;
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata){
            $microdata = new Microdata();
            $microdata->type_website(env('APP_URL'), setting('site.title'), env('APP_URL')."search?q={search_term_string}");
            $microdata->type_organization(env('APP_URL'), setting('site.title'), asset('uploads/opengraph/logo.png'), []);
            return $microdata->generate(['website', 'organization']);
        });
        // Microdata END


        /*
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
            ["css/contact.min.css", false],
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts = [
            ["runtime~js/contact.js", false],
            ["js/contact.js", false],

            // ["js/index.js",false],
            // ["js/index_test.js",false],
            // ["js/index_validator.js",false],

        ];

        $html = view('page/contact')->with([
            'sidebar_menu' => $sidebar_menu,
            'css' => $styles,
            'scripts' => $footer_scripts,
            'meta' => $metadata,
            //'microdata' => $microdata_result,
        ]);
        return $html;
    }

    //страница "Наши аптеки"
    public function our_pharmacies_index()
    {
        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        // Meta
        $meta_key = 'shop_locations';
        $metadata = get_meta($meta_key);
        if($metadata['title'] === ""){
            $metadata['title'] = "Наши аптеки - Сеть аптек “Ольвия”";
        }
        // Meta END

        // Microdata
        $microdata_key = 'structured_'.$meta_key;
        $microdata_result = Cache::remember($microdata_key, 3600, function () use ($metadata){
            $microdata = new Microdata();
            $microdata->type_website(env('APP_URL'), setting('site.title'), env('APP_URL')."search?q={search_term_string}");
            $microdata->type_organization(env('APP_URL'), setting('site.title'), asset('uploads/opengraph/logo.png'), []);
            return $microdata->generate(['website', 'organization']);
        });
        // Microdata END

        $cities = City::query()
            ->with('shops')
            ->get();
        // dd($cities[0]["shops"]);
        /*
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
            ["css/libs/flickity/flickity.min.css", false], // Do not delete
            ["css/our_pharmacies.min.css", false],
//            ["css/home_buf.min.css",false]
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts = [
            ["runtime~js/our_pharmacies.js", false],
            ["js/our_pharmacies.js", false],
            ["https://api-maps.yandex.ru/2.1/?load=package.standard&lang=ru-RU", false]
        ];


        $html = view('page/our_pharmacies')->with([
            'sidebar_menu' => $sidebar_menu,
            'cities' => $cities,
            'css' => $styles,
            'scripts' => $footer_scripts,
            'meta' => $metadata,
            'microdata' => $microdata_result,
        ]);
        return $html;
    }

    public function ajax_json_cities_shops()
    {

        $cities = City::query()
            ->with('shops')
            ->get();

        $response['status'] = '1';
        $response['cities'] = $cities;
        //$response['shops'] = $cities->shops;

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    // AJAX

    public function check_availability(Request $request){
        $data = $request->only('name', 'phone');
        $product_id = $request->get('id_product');
        $shop_id = $request->get('id_shop');
        $locale = $request->get('locale');

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', 'regex:/\+\d{2} \(\d{3}\) \d{3}-\d{2}-\d{2}/i'],
        ]);

        //dd($data,$product_id, $shop_id );

        try {
            $validator->validate();

            // Get product
            $item = Product::where('id', $product_id)->first();

            // Get shop
            $shop = ShopLocation::where('id', $shop_id)->first();

            if($item === null){
                $response['status'] = '0';
                $response['type'] = 'not_found';
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }

            $text_error = '';
            $text = 'Запрос о наличии'."\n";
            $text .= 'Имя: '.$data['name']."\n";
            $text .= 'Телефон: '.$data['phone']."\n";
            $text .= 'Товар ID: '.$item->id."\n";
            $text .= 'Товар Наименование: '.$item->title."\n";
            $text .= 'Магазин ID: '.$shop->id."\n";

            // ##### TG #####
            // ### Send telegram -> sub
            $chat_id = $shop->telegram_chat_id;
            // Send message
            if($chat_id !== null){
                $text = $text_error.$text;
                $telegram_response = Telegram::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $text,
                    //'parse_mode' => 'markdown'
                ]);
                $message_id = $telegram_response->getMessageId();
            }else{
                $text_error .= 'ВНИМАНИЕ - НЕТ ЧАТА ДЛЯ МАГАЗИНА!'."\n";
            }

            // ### Send telegram -> main
            $chat_id = setting('site.telegram_chat_id');
            if($chat_id !== null){
                $_chat_id = explode('|', $chat_id);
                foreach ($_chat_id as $chat_id){
                    // Send message
                    $telegram_response = Telegram::sendMessage([
                        'chat_id' => trim($chat_id),
                        'text' => $text_error.$text,
                        //'parse_mode' => 'markdown'
                    ]);
                    $message_id = $telegram_response->getMessageId();
                }
            }else{
            // ### Send telegram -> emergency
                $chat_id = 738876893; // Ancient
                $text_error .= 'ОШИБКА - НЕТ ТЕЛЕГРАМА В НАСТРОЙКАХ!'."\n";
                // Send message
                $text = $text_error.$text;
                $telegram_response = Telegram::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $text,
                    //'parse_mode' => 'markdown'
                ]);
                $message_id = $telegram_response->getMessageId();
            }
            // ##### TG END #####

            $response['status'] = '1';
            //$response['type'] = 'verification';
            //$response['content'] = '';
            return json_encode($response,JSON_UNESCAPED_UNICODE);

        }catch (ValidationException $e){
            $result = $validator->errors();

            $response['status'] = '0';
            $response['type'] = 'validation';
            $response['content'] = $result;
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }
    }

    function contact_submit(Request $request){
        $data = $request->only('name', 'email', 'message');

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'min:3', 'max:32'],
            'email' => ['required', 'string', 'email', 'max:64'],
            'message' => ['required', 'string', 'min:6', 'max:1000'],
        ]);

        try {
            $validator->validate();

            // Mail
            $text = "Контактная форма заполнена"."\n";
            $text .= "Имя: ".$data['name']."\n";
            $text .= "Email: ".$data['email']."\n";
            $text .= "Сообщение: ".$data['message']."\n";
            $to_email = setting('site.email_for_forms');
            if($to_email === null){
                $to_email = 'ancient.animated@gmail.com';
            }
            $transport = (new Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_ENCRYPTION')))
                ->setUsername(env('MAIL_USERNAME'))
                ->setPassword(env('MAIL_PASSWORD'))
            ;
            // Create the Mailer using your created Transport
            $mailer = new Swift_Mailer($transport);
            // Create a message
            $message = (new Swift_Message('Контактная форма заполнена'))
                ->setFrom([env('MAIL_USERNAME') => env('MAIL_FROM_NAME')])
                ->setTo([$to_email])
                ->setBody($text)
            ;
            // Send the message
            $result = $mailer->send($message);

            // ##### TG #####
            // ### Send telegram -> main
            $chat_id = setting('site.telegram_chat_id');
            if($chat_id !== null){
                $_chat_id = explode('|', $chat_id);
                foreach ($_chat_id as $chat_id){
                    // Send message
                    $telegram_response = Telegram::sendMessage([
                        'chat_id' => trim($chat_id),
                        'text' => $text,
                        //'parse_mode' => 'markdown'
                    ]);
                    $message_id = $telegram_response->getMessageId();
                }
            }else{
                // ### Send telegram -> emergency
                $chat_id = 738876893; // Ancient
                $text_error = 'ОШИБКА - НЕТ ТЕЛЕГРАМА В НАСТРОЙКАХ!'."\n";
                // Send message
                $telegram_response = Telegram::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $text_error.$text,
                    //'parse_mode' => 'markdown'
                ]);
                $message_id = $telegram_response->getMessageId();
            }
            // ##### TG END #####


            if($result === 1){
                $response['status'] = '1';
                //$response['content'] = ''; // Success message
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response['status'] = '0';
                $response['type'] = 'mail_fail';
                $response['content'] = 'Mail server error, try later'; // Error message
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }




        }catch (ValidationException $e){
            $result = $validator->errors();

            $response['status'] = '0';
            $response['type'] = 'validation';
            $response['content'] = $result;
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

    }

    function callback_submit(Request $request){
        $data = $request->only('name', 'phone');

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'min:3', 'max:32'],
            'phone' => ['required', 'regex:/\+\d{2} \(\d{3}\) \d{3}-\d{2}-\d{2}/i'],
        ]);

        try {
            $validator->validate();

            // Mail
            $text = "Запрошен обратный звонок"."\n";
            $text .= "Имя: ".$data['name']."\n";
            $text .= "Тел.: ".$data['phone']."\n";
            $to_email = setting('site.email_for_forms');
            if($to_email === null){
                $to_email = 'ancient.animated@gmail.com';
            }
            $transport = (new Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_ENCRYPTION')))
                ->setUsername(env('MAIL_USERNAME'))
                ->setPassword(env('MAIL_PASSWORD'))
            ;
            // Create the Mailer using your created Transport
            $mailer = new Swift_Mailer($transport);
            // Create a message
            $message = (new Swift_Message('Запрошен обратный звонок'))
                ->setFrom([env('MAIL_USERNAME') => env('MAIL_FROM_NAME')])
                ->setTo([$to_email])
                ->setBody($text)
            ;
            // Send the message
            $result = $mailer->send($message);


            // ##### TG #####
            // ### Send telegram -> main
            $chat_id = setting('site.telegram_chat_id');
            if($chat_id !== null){
                $_chat_id = explode('|', $chat_id);
                foreach ($_chat_id as $chat_id){
                    // Send message
                    $telegram_response = Telegram::sendMessage([
                        'chat_id' => trim($chat_id),
                        'text' => $text,
                        //'parse_mode' => 'markdown'
                    ]);
                    $message_id = $telegram_response->getMessageId();
                }
            }else{
                // ### Send telegram -> emergency
                $chat_id = 738876893; // Ancient
                $text_error = 'ОШИБКА - НЕТ ТЕЛЕГРАМА В НАСТРОЙКАХ!'."\n";
                // Send message
                $telegram_response = Telegram::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $text_error.$text,
                    //'parse_mode' => 'markdown'
                ]);
                $message_id = $telegram_response->getMessageId();
            }
            // ##### TG END #####


            if($result === 1){
                $response['status'] = '1';
                //$response['content'] = ''; // Success message
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response['status'] = '0';
                $response['type'] = 'mail_fail';
                $response['content'] = 'Mail server error, try later'; // Error message
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }




        }catch (ValidationException $e){
            $result = $validator->errors();

            $response['status'] = '0';
            $response['type'] = 'validation';
            $response['content'] = $result;
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

    }
}