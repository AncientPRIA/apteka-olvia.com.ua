<?php

namespace App\Providers;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

use TCG\Voyager\Facades\Voyager;
use App\FormFields\SelectMultipleCustomFormField;
use App\Models\TranslatableString;
use Illuminate\Support\Facades\View;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;

use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        Voyager::addFormField(SelectMultipleCustomFormField::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Specified key was too long error (on migrations)
        //Schema::defaultStringLength(191);

        /* View namespaces */
        app('view')->addNamespace('mail', resource_path('views/vendor/mail') . '/html');
        /* View namespaces END */

        /* Blade extend */
        // --- @pushonce --- //
        // Usage:
        //@pushonce('javascript:acf')
        //@endpushonce
        //@section('javascript')
        //@stop
        Blade::directive('pushonce', function ($expression) {
            $domain = explode(':', trim(substr($expression, 1, -1)));
            $push_name = $domain[0];
            $push_sub = $domain[1];
            $isDisplayed = '__pushonce_'.$push_name.'_'.$push_sub;
            return "<?php if(!isset(\$__env->{$isDisplayed})): \$__env->{$isDisplayed} = true; \$__env->startPush('{$push_name}'); ?>";
        });
        Blade::directive('endpushonce', function ($expression) {
            return '<?php $__env->stopPush(); endif; ?>';
        });

        // --- @svg --- //
        //usage:
        //@svg('{path_from_public}')
        //@svg('cloud.svg')
        Blade::directive('svg', function($arguments) {
            $path = trim($arguments, "' ");
            $code = '<?php
            $file_path = public_path('.$path.');
            $file_content = "";
            if(file_exists($file_path)){
                $file_content = file_get_contents($file_path);
            }
            echo $file_content;
            ?>';

            return $code;
        });
        /* Blade extend END */

        // Language
        /*
        $locales = config('voyager.multilingual.locales');
        if(isset($_COOKIE['lang']) AND array_search($_COOKIE['lang'], $locales) !== false){
            $current_locale = $_COOKIE['lang'];
        }else{
            $current_locale = config('voyager.multilingual.default');
        }
        App::setLocale($current_locale);
        */
        // Language END

        // Language
        if(isset($_POST['locale'])){
        // Locale found in POST and correct
            if(in_array($_POST['locale'], config('voyager.multilingual.locales'))){
                $define_locale = $_POST['locale'];
                $locale_slug = $define_locale.'/';

        // Locale found in POST and NOT correct
            }else{
                $define_locale = config('app.locale_front');
                $locale_slug = '';
            }

        // Locale found in url and correct
        }elseif(in_array(Request::segment(1), config('voyager.multilingual.locales'))){
            $define_locale = Request::segment(1);
            $locale_slug = $define_locale.'/';

        // Locale not found in url
        }else{
            $define_locale = config('app.locale_front');
            $locale_slug = '';
        }


        config([ 'app.locale_url_slug' => $locale_slug, 'app.locale_current' => $define_locale]);
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        App::setLocale($define_locale);
        Carbon::setLocale($define_locale);
        // Language END

        // Strings
        $strings = Cache::remember('strings_'.$define_locale, 86400, function (){
            $string_model = new TranslatableString();
            return $string_model->get_all_key_value();
        });
        // Strings END

        // Store in config for global use
        Config::set('strings', $strings);

        View::share(['strings' => $strings, 'locale' => $define_locale, 'locale_url_slug'=> $locale_slug]);

        // DB Query log
        /*
        DB::listen(function($query) {
            File::append(
                storage_path('/logs/query.log'),
                $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
            );
        });
        */
    }
}
