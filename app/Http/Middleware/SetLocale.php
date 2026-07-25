<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Arabic pages live under the /ar URL prefix; everything else is English.
     * The locale drives translations AND text direction (dir="rtl" in layouts).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1) === 'ar' ? 'ar' : 'en';

        app()->setLocale($locale);

        return $next($request);
    }
}
