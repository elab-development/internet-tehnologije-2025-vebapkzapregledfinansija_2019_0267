<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user=$request->user();
        if(!$user){
            return response()->json(['message'=>'Niste ulogovani'],401);
        }
        if($user->uloga!=='admin'){
            return response()->json(['message'=>'Nemate pristup, niste admin'],403);
        }
        return $next($request);
    }
}
