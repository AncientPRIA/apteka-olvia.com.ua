<?php

namespace App\Http\Controllers;

use App\Helpers\Microdata;
use App\Models\City;
use App\Models\Country;
use App\Models\Product;
use App\Models\Review;
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

class ReviewsController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function review_submit(Request $request){
        $data = $request->only('name', 'email', 'review');
        $product_id = $request->get('product_id');

        $locale = $request->get('locale');

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:32'],
            'review' => ['required', 'string', 'max:1000'],
        ]);

        //dd($data,$product_id, $shop_id );

        try {
            $validator->validate();

            $review = new Review();
            $review->name = $data['name'];
            $review->email = $data['email'];
            $review->body = $data['review'];
            $review->product_id = $product_id;
            $review->save();

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


    public function load_more(Request $request){
        $params = $request->only(['page', 'product_id']);

        if($params['page'] === null){
            $response['status'] = '0';
            $response['content'] = 'Error: page is null';
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

        $items = Review::where('product_id', $params['product_id'])
            ->orderBy('created_at', 'desc')
            ->published()
            ->paginate(null, $columns = ['*'], $pageName = 'page', $params['page']+1);
        $html = '';
        foreach ($items as $item){
            $html .= view('blocks.reviews.items.review_item')->with([
                'item' => $item,
            ]);
        }


        $response['status'] = '1';
        $response['content'] = $html;
        $response['count'] = count($items);
        return json_encode($response,JSON_UNESCAPED_UNICODE);
    }

}