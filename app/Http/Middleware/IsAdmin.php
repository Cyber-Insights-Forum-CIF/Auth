<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if(Auth::check()){

            if(Auth::user()->role == 'admin'){
                return $next($request);
            } else
            {
                return response()->json(['error' => 'You are not admin to access this'], 403);
            }

        } else {
            return response()->json(['error' => 'Not Found'], 404);

        }





    //     if (Auth::user() &&  Auth::user()->role = "admin" ) {

    //    }

       return response()->json(['error' => 'You have not admin access'], 403);
    }
}
