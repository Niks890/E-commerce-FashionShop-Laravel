<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    // Xử lý middleware xoá cache khi logout
    // public function handle(Request $request, Closure $next)
    // {
    //     $response = $next($request);
    //     return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
    //                     ->header('Pragma', 'no-cache')
    //                     ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    // }

    public function handle(Request $request, Closure $next)
{
    $response = $next($request);

    if (method_exists($response, 'headers')) {
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    return $response;
}

}
