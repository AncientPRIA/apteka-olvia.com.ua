<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Artisan as Artisan;

class DisableCache
{
    public function handle($request, Closure $next)
    {
        //Artisan::call('view:clear');
        return $next($request);
    }
}