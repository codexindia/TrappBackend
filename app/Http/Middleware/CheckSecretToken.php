<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSecretToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000');
        $request->headers->set('secret', 'hellothisisocdexindia');
        if ($request->header('secret') == 'hellothisisocdexindia') {
             return $next($request);
        }
        return $next($request);
        // return response()->json([
        //         'status' => false,
        //         'message' => 'Invalid Secret Token',
        //  ]);
       
    }
}
