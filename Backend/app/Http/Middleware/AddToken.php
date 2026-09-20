<?php

namespace App\Http\Middleware;

use Closure;

class AddToken
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        $token = isset($_COOKIE["jwt_token"])?$_COOKIE["jwt_token"]:"";
        //$request['token'] = $token;//this is working
        $request->headers->set("Authorization", "Bearer $token");//this is working
        return $next($request);
    }
}
