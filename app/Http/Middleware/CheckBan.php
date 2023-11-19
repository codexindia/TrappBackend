<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $users = User::find($request->user()->id)->UserBlocked;
        if ($users != null) {
            return response()->json([
                'status' => false,
                'blocked' => true,
                'Message' => $users->reason,
            ]);
        }
        return $next($request);
    }
}
