<?php

namespace App\Http\Controllers;
use App\Models\Subscriber;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;



class SubscriptionController extends Controller
{

    public function subscribe(Request $request){
        $data = $request->only('email');

        $validator = Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:32'],
        ]);

        try {
            $validator->validate();

            $subscriber = Subscriber::query()
                ->where('email', $data['email'])
                ->first();
            if($subscriber !== null){
                $response['status'] = '0';
                $response['type'] = 'exists';
                $response['content'] = '';
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }

            $subscriber = new Subscriber([
                'email'=> $data['email']
            ]);
            $bool = $subscriber->save();

            if($bool){
                $response['status'] = '1';
                $response['content'] = '';
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response['status'] = '0';
                $response['content'] = 'Saving error';
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