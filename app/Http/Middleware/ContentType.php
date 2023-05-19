<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ContentType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('content-type', 'text/html; charset=UTF-8');
        return $response;
    }
}
