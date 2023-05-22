<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsTokenActive
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
        $headers = apache_request_headers();
        $beartoken = $headers['Authorization'];
        $actvtoken = auth()->user()->active_token;
        if ($beartoken == "Bearer $actvtoken") {
            return $next($request);
        } else {

            $res = [
                "msg" => "User Unauthorized"
            ];
            return response()->json($res, 400);
        }
    }
}
