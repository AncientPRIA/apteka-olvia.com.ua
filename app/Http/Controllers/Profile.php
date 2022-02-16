<?php

namespace App\Http\Controllers;

use App\Helpers\Microdata;
use App\Models\Country;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

use App\Models\Product;
use App\Models\ProductCategory;

use App\Models\TranslatableString;
use App\Models\Post;
use App\Models\Metadata;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use TCG\Voyager\Models\Translation;

class Profile extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(){

        $user = \Auth::user();
        if($user === null){
            //Redirect::route('login_'.$locale);
            //redirect('login');
            return redirect()->route('home');
        }
        //dd($user);

        $sidebar_menu = ProductCategory::menu_tree_simple(null, null);

        // Favorites
        $strArrIds = array();
        $favorites = isset($_COOKIE["favorites_list"])? json_decode($_COOKIE["favorites_list"], true): [];
        foreach($favorites as $key){
            $strArrIds[] = $key["id"];
        }
        $favorites = Product::query()
            ->published()
            ->whereIn('id', $strArrIds)
            ->get();

        // Orders history
        $history_list = [];
        $orders = Order::query()
            ->where('user_id', $user->id)
            ->with('items.item')
            ->orderBy('created_at', 'DESC')
            ->get();

        //dd($orders);

        foreach ($orders as $order){

            $history_list[] = [
                'date' => $order->created_at,
                'price_full' => $order->price_full,
                'items' => $order['items'],
            ];
        }

        // Meta
        $meta_key = 'crm_profile_browse';
        $metadata = get_meta($meta_key);
        // Meta END

        /* 
            single page styles
                true - include libs
                false - my css
        */
        $styles = [
            ["css/profile.min.css",false]
        ];

        $footer_scripts=[
            ["runtime~js/profile.js",false],
            ["js/profile.js",false],
            ["runtime~js/user_active.js",false],
            ["js/user_active.js",false]
        ];

        $html = view('page/user')->with([
            'css' => $styles,
            'sidebar_menu'=> $sidebar_menu,
            'scripts' =>  $footer_scripts,
            'favorites' => $favorites,
            'history' => $history_list,

            'meta' => $metadata,
            'user' => $user,
            ]);
        return $html;
    }

}
