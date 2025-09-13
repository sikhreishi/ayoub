<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLanguage
{

    public function handle(Request $request, Closure $next)
    {
        $locale = session('lang', 'en');

        app()->setLocale($locale);

        return $next($request);
    }
}
