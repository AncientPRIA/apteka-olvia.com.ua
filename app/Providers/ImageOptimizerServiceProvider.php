<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
//use App\Processors\ImageOptimizer;

class ImageOptimizerServiceProvider extends ServiceProvider
{

    public $base_path;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('imageoptimizer', function()
        {
            return new \App\Processors\ImageOptimizer;
        });
    }

    /**
     *
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
