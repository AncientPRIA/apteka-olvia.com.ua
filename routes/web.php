<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/* COMMANDS */
Route::get('/cache-clear', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

//Clear All
Route::get('/clear', function() {
    $exitCode = Artisan::call('cache:clear');
    //$exitCode = Artisan::call('optimize');
    //$exitCode = Artisan::call('route:cache');
    $exitCode = Artisan::call('route:clear');
    //$exitCode = Artisan::call('route:cache');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('config:clear');
    //$exitCode = Artisan::call('config:cache');

    return '<h1>All cleared</h1>';
});

Route::get('/config-publish', function() {
    $exitCode = Artisan::call('vendor:publish');
    return '<h1>Configs published</h1>';
});

// Clear images table and all cache
Route::get('/clear_cached_images', 'Api\ImageOptimizationController@clear_cached_images');
/* COMMANDS END */

/* ADMIN */
Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    // Admin - Translations
    //Route::get('translations', 'AdminTranslationsController')->name();
    Route::group([
        'middleware'    => 'admin.user',
        'as'            => 'voyager.translations.',
        'prefix'        => 'translations',
    ], function () {
        Route::get('/', ['uses' => 'AdminTranslationsController@index', 'as' => 'index']);
        //Route::get('/', 'AdminTranslationsController@index');
    });

});
/* ADMIN END */

/* AJAX */
/*
Route::group(['middleware' => 'json'], function(){
    Route::post('/csrf_refresh', 'HomeController@csrf_refresh');
});
*/
/* AJAX END */

/*
Route::get('/logout', function(){
    Auth::logout();
    //return Redirect::to('login');
});
*/

$locale = config('app.locale_front');

/* AUTH */
//Auth::routes();

// Password Reset Routes...
Route::get(Lang::get('routes.password.reset',[], $locale), 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request_'.$locale); // Shows request reset form
Route::post(Lang::get('routes.password.email',[], $locale), 'Auth\ForgotPasswordController@sendResetLinkEmailJson')->name('password.email_'.$locale);
Route::get(Lang::get('routes.password.reset',[], $locale).'/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post(Lang::get('routes.password.reset',[], $locale), 'Auth\ResetPasswordController@reset')->name('password.update_'.$locale);

// Email Verification Routes...
Route::get(Lang::get('routes.email.verify',[], $locale), 'Auth\VerificationController@show')->name('verification.notice_'.$locale);        // Shows blade
Route::get(Lang::get('routes.email.verify',[], $locale).'/{id}/{email_verification_token}', 'Auth\VerificationController@verify')->name('verification.verify_'.$locale); // Verifies (link from email)
Route::get(Lang::get('email.resend',[], $locale), 'Auth\VerificationController@resend')->name('verification.resend_'.$locale);

// Authentication Routes...
//Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
//Route::post('login', 'Auth\LoginController@login'); // default login
//Route::post('auth', 'Auth\LoginController@authenticate'); // custom login // put in json

//Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
//Route::get('register', function (){abort(404);}/*'Auth\RegisterController@showRegistrationForm'*/)->name('register');
//Route::post('register', 'Auth\RegisterController@register');

//
//Route::get('/login', 'HomeController@login')->name('login');

Route::get('/logout', function(){
    Auth::logout();
    return Redirect::to('/');
});
Route::post('/logout', function(){
    Auth::logout();
    return Redirect::to('/');
})->name('logout');

// Socialite

Route::get('login/google', 'Auth\LoginController@redirectToProvider')->name('login_google');
Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('login/vkontakte', 'Auth\LoginController@redirectToProvider')->name('login_vkontakte');
Route::get('login/vkontakte/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('login/facebook', 'Auth\LoginController@redirectToProvider')->name('login_facebook');
Route::get('login/facebook/callback', 'Auth\LoginController@handleProviderCallback');
Route::get('login/instagram', 'Auth\LoginController@redirectToProvider')->name('login_instagram');;
Route::get('login/instagram/callback', 'Auth\LoginController@handleProviderCallback');

/* AUTH END */


/* TRY NAME PREFIXING FOR LANG */
/* Example
Route::name('admin.')->group(function () {
    Route::get('users', function () {
        // Route assigned name "admin.users"...
    })->name('users');
});
*/
/* WEB */
// All static routes must be translated in resources/lang/{locale}/routes.php

Route::get('/' , 'HomeController@index')->name('home');
Route::get('/about' , 'AboutController@index')->name('about');
Route::get('/contact' , 'ContactController@index')->name('contact');
Route::get('/cart' , 'CartController@index')->name('cart');
Route::get('/our_pharmacies' , 'ContactController@our_pharmacies_index')->name('shops_locations');
Route::get('/products' , 'ProductsController@archive')->name('products');
Route::get('/products/{path}' , 'ProductsController@index')->where('path', '(.*)');
Route::get('/discount_products' , 'ProductsController@discount_product_index')->name('discount_products');
Route::get('/blog' , 'BlogController@archive')->name('blog');
Route::get('/blog/{path}' , 'BlogController@index')->where('path', '(.*)');
Route::get('/blog_single' , 'BlogController@test_single')->name('blog_single');
Route::get('/test2' , 'HomeController@test2');
Route::get('/jobs', 'HomeController@jobs')->name('jobs');
Route::get('/partners', 'HomeController@partners')->name('partners');
Route::post('/get-produts-list' , 'CartController@ajax_get_products')->name('get_produts_list');

Route::get('/generate_sitemap', 'Api\ToolsController@generate_sitemap');



/* user */

Route::get('/profile' , 'Profile@index')->name('profile');

/* search */
Route::get('/search' , 'SearchController@index')->name('search');

/*
// Categories
$categories = \App\Models\ProductCategory::query()
    ->where('parent_id', null)
    ->get();
foreach ($categories as $category){
    $products = $category->products();
    Route::get('/products'.'/'.$category->slug , 'ProductsController@archive')->name('category_'.$category->id);
    foreach ($products as $product){
        Route::get('/products'.'/'.$category->slug.'/'.$product->slug , 'HomeController@test2')->name('product_'.$product->id);
    }
}
*/

/* WEB END */

/* AJAX */
Route::group(['middleware' => 'json'], function() {
    Route::post('auth', 'Auth\LoginController@authenticate'); // custom login
    Route::post('register', 'Auth\RegisterController@registers'); // custom register
    Route::post('profile_update', 'Auth\RegisterController@profile_update'); // update profile info

    Route::post('ajax/products/load_more', 'ProductsController@load_more');
    Route::post('ajax/blog/load_more', 'BlogController@load_more');
    Route::post('ajax/reviews/load_more', 'ReviewsController@load_more');

    Route::post('ajax/smart_search', 'SearchController@smart_search');
    Route::post('ajax/favorite', 'CartController@set_favorite_user');
    Route::post('ajax/cities_shops', 'ContactController@ajax_json_cities_shops');
    Route::post('ajax/check_availability', 'ContactController@check_availability');
    Route::post('ajax/review_submit', 'ReviewsController@review_submit');
    Route::post('ajax/contact_submit', 'ContactController@contact_submit');
    Route::post('ajax/callback_submit', 'ContactController@callback_submit');
    Route::post('ajax/product_rate', 'ProductsController@product_rate');


    Route::post('cart_submit', 'CartController@cart_submit');

    Route::post('subscribe', 'SubscriptionController@subscribe');

    //polit
    Route::post('/polit', 'HomeController@polit');
    Route::post('/csrf_refresh', 'HomeController@csrf_refresh');

});
/* AJAX END */

/* API */
Route::group(['prefix' => 'api'], function() {

    Route::middleware('api_token')->get('parser_xls_products', 'Api\XLSParserController@index');
    Route::middleware('api_token')->get('parse_konfeterra_ready', 'Api\HTMLParserController@parse_konfeterra_ready');
    Route::middleware('api_token')->get('parse_winestyle', 'Api\HTMLParserController@parse_winestyle');
    Route::middleware('api_token')->get('parse_krasnoeibeloe', 'Api\HTMLParserController@parse_krasnoeibeloe');
    Route::middleware('api_token')->get('parse_apteka911', 'Api\HTMLParserController@parse_apteka911');
    Route::middleware('api_token')->get('parse_eapteka', 'Api\HTMLParserController@parse_eapteka');
    Route::middleware('api_token')->get('parse_asna', 'Api\HTMLParserController@parse_asna');
    Route::middleware('api_token')->get('img_problem', 'Api\XLSParserController@img_problem');
    Route::middleware('api_token')->get('mass_optimize_products', 'Api\ImageOptimizationController@mass_optimize_products');
    Route::middleware('api_token')->get('target_optimize_products', 'Api\ImageOptimizationController@target_optimize_products');
    Route::middleware('api_token')->get('sync_test', 'Api\SynchronizerController@test');

    // VK API
    Route::middleware('api_token')->get('vkontakte/export_xls', 'Api\VKController@export_xls');
    Route::get('vkontakte/verify', 'Api\VKController@verify');

    // System Tools API
    // Remove duplicates in translatable_strings
    Route::get('translation_duplicates_remove', 'Api\ToolsController@translation_duplicates_remove');
    Route::get('remove_optics', 'Api\ToolsController@remove_optics');

    // Create sitemap
    Route::middleware('api_token')->get('generate_sitemap', 'Api\ToolsController@generate_sitemap');

    // ### Sync ###
    Route::get('xls_sync', 'Api\SynchronizerController@xls_sync');

    Route::get('products_sync', 'Api\SynchronizerController@products_sync');
    Route::get('availability_sync', 'Api\SynchronizerController@availability_sync');
    Route::get('get_sync_file', 'Api\SynchronizerController@get_sync_file');
    Route::get('products_accordance', 'Api\SynchronizerController@products_accordance');

    Route::get('test_ftp_connection', 'Api\SynchronizerController@test_ftp_connection');
    Route::get('test_products_list', 'Api\SynchronizerController@test_products_list');
    Route::get('test_availability', 'Api\SynchronizerController@test_availability');

    Route::get('create_products_sync_job', 'Api\SynchronizerController@create_products_sync_job');
    Route::get('create_availability_sync_job', 'Api\SynchronizerController@create_availability_sync_job');
    Route::get('create_products_accordance_job', 'Api\SynchronizerController@create_products_accordance_job');
    // ### Sync END ###

    // ### Search ###
    Route::post('multi_search', 'SearchController@multi_search');
    Route::get('test_multi_search', 'SearchController@test_multi_search');
    // ### Search END ###


});
/* API END */

/* TEST */
Route::get('test', 'HomeController@test');
//Route::get('emngetf', 'HomeController@countries'); // countries
/* TEST END */