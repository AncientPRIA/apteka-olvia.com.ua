<?php


namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ImageOptimizerFacade extends Facade
{
    protected static function getFacadeAccessor() { return 'imageoptimizer'; }
}