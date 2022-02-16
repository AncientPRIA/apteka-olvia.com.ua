<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrdersItem;
use App\Models\ProductCategory;
use App\Models\ShopLocation;
use App\Models\Wishlist;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Telegram\Bot\Laravel\Facades\Telegram;

class CartController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(){
        $user = \Auth::user();


        //dd(\Auth::user(), \Auth::check(), $user->hasVerifiedEmail());


        // Products - Featured (Season)
        $products_featured = Product::query()
            ->orderBy('created_at')
            ->limit(10)
            ->with("category")
            ->featured()
            ->get();
//
//
//        $cities = City::query()
//            ->with('shops')
//            ->get();

        $cities = City::query()
            ->with('shops')
            ->get();

        // Meta
        $meta_key = 'home';
        $metadata = get_meta($meta_key);
        // Meta END

        // Microdata
        /*
        $microdata_result = Cache::remember('index_structured', 3600, function () use ($meta){
            $microdata = new Microdata();
            $microdata->type_store('Rave', $meta['description'], $meta['og_image'], 'Mo-Su 11:00-22:00', '', "г.Донецк, Университетская 20");
            return $microdata->generate(['store']);
        });
        */
        // Microdata END



        /*
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
//            ["css/libs/flickity/flickity.min.css",false], // Do not delete
            ["css/cart.min.css",false],
//            ["css/home_buf.min.css",false]
        ];
        /*
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            ["runtime~js/cart_single_page.js",false],
            ["js/cart_single_page.js",false],
            ["js/index_validator.js",false],
        ];

//        if($user === true){
//            array_push($footer_scripts, ["runtime~js/user_active.js",false],["js/user_active.js",false]);
//        }

        if(isset($_COOKIE["product_list"])){
            $cartList = json_decode($_COOKIE["product_list"], true);
        }else{
            $cartList = array();
        }

        $strArrIds = array();
        $arrCountProduct = array();

        foreach ($cartList as $key){
            $strArrIds[] = $key["id"];
            $arrCountProduct [$key["id"]] = $key["count"];
        }

        $products = Product::query()
            ->whereIn('id', $strArrIds)
            ->get();

        $cart_product_list="";


        foreach($products as $key){
            // $html .= view(('page/home'));
            $cart_product_list .= view('blocks/modal_basket/items/modal_basket_item')->with(
                [   'content' => $key,
                    'count'=> $arrCountProduct[$key['id']]
                ]);
        }

//        dd($html);

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        $html = view('page/cart')->with([
            'products_featured' => $products_featured,
            'cart_product_list' => $cart_product_list,
            'user' => $user,
            'cities' => $cities,
            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'meta' => $metadata,
            'sidebar_menu' => $sidebar_menu
            //'microdata' => $microdata_result,
        ]);
        return $html;
    }

    public function ajax_get_products(){

        if(isset($_POST['cart'])){
            $cartList = json_decode($_POST['cart'], true);
        }else{
            $cartList = array();
        }

        $strArrIds = array();
        $arrCountProduct = array();

        foreach ($cartList as $key){
            $strArrIds[] = $key["id"];
            $arrCountProduct [$key["id"]] = $key["count"];
        }

        $products = Product::query()
            ->whereIn('id', $strArrIds)
            ->get();

        $html="";


        foreach($products as $key){
            // $html .= view(('page/home'));
            $html .= view('blocks/modal_basket/items/modal_basket_item')->with(
                [   'content' => $key,
                    'count'=> $arrCountProduct[$key['id']]
                ]);
        }

        $response['status'] = '1';
        $response['content'] = $html;

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }


    public function get_favorite_user(){
        $user = user_verified();
        if(!$user){
            $response['status'] = '0';
            $response['content'] = '';
        }else{

            $response['status'] = '1';
            $response['content'] = json_encode($user, JSON_UNESCAPED_UNICODE);
        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function set_favorite_user(Request $request){
        $user = user_verified();
        $data = $request->get('favorit');
        $data = json_decode($data, true);

        if(!$user){
            $response['status'] = '0';
            $response['content'] = '';
        }else{
            $user_wishlist = Wishlist::query()
                ->where('user_id', '=', $user->id)
                ->get();
            //$products_to_add = [];

            foreach ($user_wishlist as $key=>$wishlist_item){
                $found = array_search($wishlist_item->product_id, array_column($data, 'id'));
                if($found === false){
                    //$items_to_remove = [$wishlist_item->id];
                    //$user_wishlist->forget($key);
                }else{
                    $user_wishlist->forget($key);
                    unset($data[$found]);
                    //$products_to_add[] = $data[$found];
                }
            }

            Wishlist::destroy($user_wishlist->pluck('id'));
            foreach ($data as $item){
                $saving = new Wishlist();
                $saving->user_id = $user->id;
                $saving->product_id = $item['id'];
                $saving->save();
            }

            dd($user_wishlist, $data);

            $response['status'] = '1';
            $response['content'] = '';

        }

        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }


    // Submits order
    public function cart_submit(Request $request){
        $data = $request->only('name', 'phone', 'comment', 'adress');
        $user_id = $request->get('user_id');
        $locale = $request->get('locale');

        $user = Auth::user();
        //var_dump($user->id);
        $user_valid = false;
        if($user !== null && $user->id === (int)$user_id){
            // Order will be attached to user
            $user_valid = true;
        }

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', 'regex:/\+\d{2} \(\d{3}\) \d{3}-\d{2}-\d{2}/i'],
            'comment' => ['nullable' ,'string', 'max:1000'],
            'adress' => ['required', 'numeric'],
        ]);

        try {
            $validator->validate();

            if(isset($_COOKIE["product_list"])){
                $product_list = json_decode($_COOKIE["product_list"], true);
            }else{
                $product_list = array();
            }
            if(count($product_list) === 0){
                $response['status'] = '0';
                $response['type'] = 'empty';
                $response['content'] = 'Корзина пуста';
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }

            // Get shop and city
            $shop_id = $data['adress'];
            $shop = ShopLocation::query()
                ->where('id', $shop_id)
                ->with('city')
                ->first()
            ;
            if($shop === null){
                $response['status'] = '0';
                $response['type'] = 'shop_empty';
                $response['content'] = 'Не выбран магазин';
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }

            // Create an order
            $form_data = json_encode($data);
            $price_total = 0;
            $order = new Order();
            if($user_valid){
                $order->user_id = $user->id;
            }
            $order->form_data = $form_data;
            $order->save();

            // Create an order item for each product, assign them order id
            $saved_order_items = [];
            $text_error = '';
            $text = 'Оформлен новый заказ!'."\n";
            $text .= 'Имя: '.$data['name']."\n";
            $text .= 'Телефон: '.$data['phone']."\n";
            $text .= 'Город: '.$shop->city->name."\n";
            $text .= 'Адрес: '.$shop->address."\n";
            if($data['comment'] !== null){
                $text .= 'Комментарий: '.$data['comment']."\n";
            }
            $text .= '### Заказ ###'."\n";
            $text .= '------------------'."\n";


            foreach ($product_list as $ordered_product){
                $order_item = new OrdersItem();
                $order_item->order_id = $order->id;
                if(!isset($ordered_product['price']) || $ordered_product['price'] === null){
                    $ordered_product['price'] = 0;
                }

                $product = Product::query()
                    ->published()
                    ->where('id', $ordered_product['id'])
                    ->first();
                if($product === null){ // Clear if no product
                    foreach ($saved_order_items as $saved_order_item){
                        $saved_order_item->delete();
                    }
                    $order->delete();
                    setcookie('product_list', '', time() - 3600, '/');

                    $response['status'] = '0';
                    $response['type'] = 'unacceptable';
                    $response['content'] = 'Недопустимый товар в корзине';
                    return json_encode($response,JSON_UNESCAPED_UNICODE);
                }
                $product->selled_amount += $ordered_product['count'];
                $product->save();
                $order_item->product_id = $ordered_product['id'];
                $order_item->title = $product->title;
                $order_item->price = $ordered_product['price'];
                $order_item->count = $ordered_product['count'];
                $order_item->save();
                $saved_order_items[] = $order_item;
                if(isset($product["discount"]) and $product["discount"] != 0 ){
                    $price_discounted = round($product['price'] - ($product['price'] * ($product["discount"] / 100)));
                }else{
                    $price_discounted = $product['price'];
                }

                $price_total += ($price_discounted * $ordered_product['count']);

                $text .= 'ID товара: '.$product->id."\n";
                $text .= 'Название: '.$product->title."\n";
                $text .= 'Кол-во: '.$ordered_product['count']."\n";
                $text .= 'Цена за 1 ед.: '.$price_discounted."\n";
                $text .= '------------------'."\n";
            }
            $order['price_full'] = $price_total;
            $order->save();
            $text .= 'Всего: '.$price_total."\n";
            $text .= '##########'."\n";

            // Remove order from cookies
            setcookie('product_list', '', time() - 3600, '/');

            // ### Notify ###
            /*
            // Mail
            //$emails = setting('site.order_emails'); // explode by | and trim
            $transport = (new Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_ENCRYPTION')))
                ->setUsername(env('MAIL_USERNAME'))
                ->setPassword(env('MAIL_PASSWORD'))
            ;
            // Create the Mailer using your created Transport
            $mailer = new Swift_Mailer($transport);
            // Create a message
            $message = (new Swift_Message('Заказ оформлен'))
                ->setFrom([env('MAIL_USERNAME') => env('MAIL_FROM_NAME')])
                ->setTo(['ancient.animated@gmail.com'])
                ->setBody($text)
            ;
            // Send the message
            $result = $mailer->send($message);
            */

            // TG
            // TG -> sub
            $chat_id = $shop->telegram_chat_id;
            if($chat_id !== null){

                $telegram_response = Telegram::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $text_error.$text,
                    //'parse_mode' => 'markdown'
                ]);
                $message_id = $telegram_response->getMessageId();
            }else{
                $text_error .= 'ВНИМАНИЕ - НЕТ ЧАТА ДЛЯ МАГАЗИНА!'."\n";
            }

            // TG -> main
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
            // TG -> emergency
                $chat_id = 738876893; // Ancient
                $text_error .= 'ОШИБКА - НЕТ ТЕЛЕГРАМА В НАСТРОЙКАХ!'."\n";
                $text = $text_error.$text;
                $telegram_response = Telegram::sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $text,
                    //'parse_mode' => 'markdown'
                ]);
                $message_id = $telegram_response->getMessageId();
            }
            // TG END


            $response['status'] = '1';
            //$response['type'] = 'verification';
            $response['content'] = 'Заказ оформлен';
            return json_encode($response,JSON_UNESCAPED_UNICODE);

        }catch (ValidationException $e){
            $result = $validator->errors();

            $response['status'] = '0';
            $response['type'] = 'validation';
            $response['content'] = $result;
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

    }


}
