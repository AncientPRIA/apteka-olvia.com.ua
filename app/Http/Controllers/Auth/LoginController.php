<?php

namespace App\Http\Controllers\Auth;

use App\User as User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
//use Illuminate\Support\Facades\Validator;
//use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Used for redirect
        //$this->middleware('guest')->except('logout');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $locale = $request->get('locale');

        // Captcha
        /*
        $captcha_token = $request->only('captcha_token')['captcha_token'];
        $score = $this->captcha_check($captcha_token);
        if($score === false){
            // Captcha error
            $response['status'] = '0';
            $response['type'] = 'captcha';
            $response['content'] = 'Ошибка капчи';
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }elseif($score < 0.5){
            $response['status'] = '0';
            $response['type'] = 'captcha';
            $response['content'] = 'Капча не пройдена';
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }
        */


        $validator = Validator::make($credentials, [
            'email' => ['required', 'string', 'email', 'max:32'],
            'password' => ['required', 'string', 'min:8', 'max:32'],
        ]);

        try {
            $validator->validate();

            $remember = true;
            if(!$request->get('remember_me', false)){
                $remember = false;
            }

            if (\Auth::attempt($credentials, $remember)) {
                $user = \Auth::getUser();
                $user_verified = user_verified();
                if(!$user_verified){
                    // Not verified
                    $user->sendEmailVerificationNotification();
                    $response['status'] = '0';
                    $response['type'] = 'verification';
                    $response['content'] = 'Верификация';
                    return json_encode($response,JSON_UNESCAPED_UNICODE);
                }else{
                    // Authentication passed...
                    $response['status'] = '1';
                    $response['redirect'] = route('home');
                    $response['content'] = 'Вход выполнен успешно';
                    return json_encode($response,JSON_UNESCAPED_UNICODE);
                }
            }else{
                $response['status'] = '0';
                $response['type'] = 'wrong';
                $response['content'] = 'Неверный эмеил или пароль';
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


    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider(Request $request)
    {
        $auth_string = $request->segment(2);
        return Socialite::driver($auth_string)->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        $auth_string = $request->segment(2);
        $oauth_user = Socialite::driver($auth_string)->user();

        if(isset($oauth_user->email)){
            $email = $oauth_user->email;
        }elseif(isset($oauth_user->accessTokenResponseBody['email'])){
            $email = $oauth_user->accessTokenResponseBody['email'];
        }else{
            $email = null;
        }
        $id = $oauth_user->id;
        //dd($oauth_user, $oauth_user->name);
        $name = $oauth_user->name;

        $auth_types = config('auth.auth_types');
        $auth_type = $auth_types[$auth_string];

        if(isset($email)){

            $user = User::where(['email'=> $email, 'auth_type'=> $auth_type, 'auth_id'=> $id])->first();

            // Create and login new user
            if($user === null){
                //dd('user not found', $user);
                // Determine auth_type
                //dd($auth_types, $auth_type);

                $created_user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => '',
                    'phone' => null,
                    'auth_type' => $auth_type,
                    'auth_id' => $id,
                    'email_verified_at' => now(),
                ]);

                event(new Registered($created_user));
                $this->guard()->login($created_user);

                return redirect()->route('home');
                //return redirect()->route('home');

            }else{
                //dd('user found', $user);

                $this->guard()->login($user);

                return redirect()->route('home');
            }

            // Login user if its auth type is social
            //if($user->auth_type !== 0){

        }elseif($auth_type == 4){ // Instagram
            $user = User::where(['auth_type'=> $auth_type, 'auth_id'=> $id])->first();

            if($user === null){

                $created_user = User::create([
                    'name' => $name,
                    'email' => '',
                    'password' => '',
                    'phone' => null,
                    'auth_type' => $auth_type,
                    'auth_id' => $id,
                ]);

                event(new Registered($created_user));
                $this->guard()->login($created_user);
                return redirect()->route('home');

            }else{
                $this->guard()->login($user);
                return redirect()->route('home');
            }



        }else{
            dd('oauth failed', $oauth_user);
        }





        // $user->token;
    }

    // Get captcha result by token
    private function captcha_check($captcha_token){
        $captcha_secret = config('services.captcha.secret');
        $post = array(
            'secret' => $captcha_secret,
            'response' => $captcha_token,
        );

        $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        //curl_setopt($curl, CURLOPT_HTTPHEADER, '');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($curl);
        $res = json_decode($res);
        if($res->success !== true){
            return false;
        }else{
            $score = round(floatval($res->score), 2);
            return $score;
        }
    }
}
