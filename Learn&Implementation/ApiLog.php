<?php

namespace App\Http\Middleware;

use Closure;

class ApiLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        $response = $next($request);
        
        // Log Only in Developement Not in Production
        if(strtolower(config('app.env')) == 'local'){

            $log_response = [
                 "URI"    => $request->getUri(),
                 "METHOD" => $request->getMethod(),
                 "REQUEST_BODY" => $request->all(),
                 "RESPONSE" => json_decode($response->getContent())
             ];

            info($log_response);

        }

        return $response;
    }
}
