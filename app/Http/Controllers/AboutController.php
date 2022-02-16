<?php

namespace App\Http\Controllers;

use App\Helpers\Microdata;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductCategory;
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

use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use TCG\Voyager\Models\Category;
use TCG\Voyager\Models\Translation;

class AboutController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function index(){

        $categories = ProductCategory::query()
            ->get();


        // Meta
        $meta_key = 'crm_details_browse';
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
            ["css/libs/flickity/flickity.min.css",false], // Do not delete
            ["css/home.min.css",false],
            ["css/home_buf.min.css",false]
        ];
        /* 
            single page scripts
                true - include libs
                false - my css
        */
        $footer_scripts=[
            ["js/about.js",false],
            ["js/index_test.js",false],
        ];

        $html = view('page/about')->with([
            'css' => $styles,
            'scripts' =>  $footer_scripts,
            'meta' => $metadata,
            //'microdata' => $microdata_result,
            ]);
        return $html;
    }


    public function login(){
        // Language
        $locales = config('voyager.multilingual.locales');
        $locale = config('app.locale_current');
        // Language END

        // Meta
        $meta_key = 'crm_login';
        $metadata = get_meta($meta_key);
        // Meta END


        $html = view('page/login')->with([
            'locale' => $locale,
            'locales' => $locales,
            'meta' => $metadata,
            //'microdata' => $microdata_result,
        ]);
        return $html;
    }


    public function register(){
// Language
        $locales = config('voyager.multilingual.locales');
        $locale = config('app.locale_current');
        // Language END

        // Meta
        $meta_key = 'crm_login';
        $metadata = get_meta($meta_key);
        // Meta END

        $html = view('page/register')->with([
            'locale' => $locale,
            'locales' => $locales,
            'meta' => $metadata,
            //'microdata' => $microdata_result,
        ]);
        return $html;
    }




    // AJAX
    // Email contact
    function email_contact(Request $request){
        $form_data = $request->only('email', 'message');

        $validator = Validator::make($form_data, [
            'email' => ['required', 'string', 'email', 'max:64'],
            'message' => ['required', 'string', 'min:6', 'max:1000'],
        ]);

        try {
            $validator->validate();

            // Do something here
            $transport = (new Swift_SmtpTransport('mail.pria.digital', 465, 'ssl'))
                ->setUsername('info@pria.digital')
                ->setPassword('q1w2e3r4')
            ;

            // Create the Mailer using your created Transport
            $mailer = new Swift_Mailer($transport);

            // Create a message
            $message = (new Swift_Message('Contact'))
                ->setFrom(['info@pria.digital' => 'Workelite'])
                ->setTo(['ancient.animated@gmail.com'])
                ->setBody("Someone with email ".$form_data['email'].", sent us a question: ".$form_data['message'])
            ;

            // Send the message
            $result = $mailer->send($message);
            if($result === 1){
                $response['status'] = '1';
                $response['content'] = ''; // Success message
                return json_encode($response,JSON_UNESCAPED_UNICODE);
            }else{
                $response['status'] = '0';
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



    // Fills translations for countries. DO NOT DELETE PLEASE (Not yet...)
    /*
    public function countries(){

        $list_of_locales = [
            'ru',
            'en',
            'pl',
            'uk',
        ];

        $is_first = true;
        foreach ($list_of_locales as $locale){
            $json_file = storage_path('countries/'.$locale.'/world.json');
            if(File::exists($json_file)){

                $json = json_decode(File::get($json_file), true);
                foreach ($json as $row){
                    if($is_first){
                        //"id", "name", "alpha_2", "alpha_3"
                        Country::query()
                            ->insert([
                                'id'=>              $row['id'],
                                'name'=>            $row['name'],
                                'alpha_2'=>         $row['alpha2'],
                                'alpha_3'=>         $row['alpha3'],
                            ]);
                    }else{
                        // 'table_name', 'column_name', 'foreign_key', 'locale', 'value'
                        Translation::query()
                            ->insert([
                                'table_name'=>      'countries',
                                'column_name'=>     'name',
                                'foreign_key'=>     $row['id'],
                                'locale'=>          $locale,
                                'value'=>           $row['name']
                            ]);
                    }

                }

                $is_first = false;

            }else{
                echo 'No '.$locale.' file'.PHP_EOL;
            }
        }

    }
    */

    public function test(){

	    // Language
	    $locales = config('voyager.multilingual.locales');
	    $locale = config('app.locale_current');
	    // Language END



	    // Meta
        $meta_key = 'crm_test';
        $metadata = get_meta($meta_key);
	    // Meta END

	    // include js/css
		    $styles = [
			    //["css/libs/flickity/flickity.min.css",false],
			    ["css/reset.css",false]
		    ];
		    /*
				single page scripts
					true - include libs
					false - my css
			*/
		    $footer_scripts=[
			    //["js/libs/flickity/flickity.pkgd.min.js",false],
			    //["js/libs/wow/wow.min.js",false],
			    ["js/main.js",false],
		    ];
	    // include js/css END

	    $html = view('page/reset')->with([
		    'css' => $styles,
		    'scripts' =>  $footer_scripts,
		    'locale' => $locale,
		    'locales' => $locales,
		    'meta' => $metadata,
		    //'microdata' => $microdata_result,
	    ]);
	    return $html;
    }

}
