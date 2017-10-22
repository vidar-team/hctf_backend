<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class I18n
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('language')){
            \App::setLocale($request->input('language'));
        }
        return $next($request);
    }
}