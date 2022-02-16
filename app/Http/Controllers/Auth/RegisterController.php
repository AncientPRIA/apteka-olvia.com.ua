<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        //$this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            //'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:32', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'max:32', 'confirmed'],
            //'phone' => ['required', 'regex:/\+\d{2} \(\d{3}\) \d{3}-\d{2}-\d{2}/i']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            //'name' => $data['name'],
            'email' => $data['email'],
            'email_verification_token'=> Str::random(60),
            'password' => Hash::make($data['password']),
            //'phone' => $data['phone'],
        ]);
    }


    public function postRegister(Request $request)
    {
        return redirect('/');
    }

    public function registers(Request $request)
    {
        $locale = $request->get('locale');
        $validator = $this->validator($request->all());

        try {
            $result = $validator->validate();

            event(new Registered($user = $this->create($request->all())));
            //$user->sendEmailVerificationNotification(); // Not needed, email sends automatically
            $this->guard()->login($user);

            $response['status'] = '1';
            $response['redirect'] = route('home');
            $response['content'] = "Регистрация успешна";
            return json_encode($response,JSON_UNESCAPED_UNICODE);

        } catch (ValidationException $e) {
            $result = $validator->errors()->getMessages();

            if(isset($result['email'])){
                $response['status'] = '0';
                $response['type'] = 'exists';
                $response['content'] = $result;
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response['status'] = '0';
                $response['type'] = 'validation';
                $response['content'] = $result;
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }


        }




        /*
        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
        */
    }

    public function profile_update(Request $request){

        $data = $request->all();
        $fields = [];
        $rules = [];


        $user = Auth::user();
        //var_dump($user->id);
        if($user->id != (int)$data['user_id']){
            $response['status'] = '0';
            $response['content'] = 'ID пользователя не совпадает с текущим';
            return json_encode($response,JSON_UNESCAPED_UNICODE);
        }

        // Common fields
        /* Name */ if($data['name'] !== null){
            $fields[] =  'name';
            $rules['name'] = ['required', 'string', 'max:255'];
        }
        /* Phone */ if($data['phone'] !== null){
            $fields[] =  'phone';
            $rules['phone'] = ['required', 'regex:/\+\d{2} \(\d{3}\) \d{3}-\d{2}-\d{2}/i'];
        }

        $is_social_auth = false;
        if($user->auth_type !== "0"){
            $is_social_auth = true;
            // Social fields

        }else{
            // Local fields
            /* Email */ if($data['email'] !== null){
                if($data['email'] === $user->email){
                    $fields[] =  'email';
                    $rules['email'] = ['required', 'string', 'email', 'max:64', 'exists:users,email'];
                }else{
                    $fields[] =  'email';
                    $rules['email'] = ['required', 'string', 'email', 'max:64', 'unique:users'];
                }

            }
            /* Password */ if($data['password'] !== null){
                $fields[] =  'password';
                $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
                //$fields[] =  'password_confirmation';
                //$rules['password_confirmation'] = ['required', 'string', 'min:6', 'same:password'];
            }
        }


        $validator = Validator::make($data, $rules);

        //dd($rules, $validator, $user->auth_type);

        try {
            $validator->validate();

            $update_array = [];
            foreach ($fields as $field){
                if($field === 'password'){
                    $update_array[$field] = Hash::make($data[$field]);
                }
                if($field === 'password_confirmation'){
                    continue;
                }
                $update_array[$field] = $data[$field];
            }
            $result = User::update_user($user->id, $update_array);

            if($result){
                $response['status'] = '1';
                $response['content'] = "Профиль обновлён";
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response['status'] = '0';
                $response['content'] = "Ошибка овновления профиля";
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }



        } catch (ValidationException $e) {
            $result = $validator->errors()->getMessages();

            if(isset($result['email'])) {
                $response['status'] = '0';
                $response['type'] = 'exists';
                $response['content'] = $result;
                return json_encode($response, JSON_UNESCAPED_UNICODE);
            }else{
                $response['status'] = '0';
                $response['type'] = 'validation';
                $response['content'] = $result;
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }
        }

    }
}
