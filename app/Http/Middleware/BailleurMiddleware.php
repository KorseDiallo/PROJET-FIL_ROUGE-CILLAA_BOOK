<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BailleurMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // if (auth()->check()) {

        //     $user = auth()->user();

        //     if ($user->role === 'Bailleur') {
        //         return $next($request);
        //     }
        // } else {
        //     return response()->json([
        //         "message" => "vous n'êtes pas Connexte"
        //     ]);
        // }

        // if (auth()->check() && auth()->user()->role === 'Bailleur') {
        if (Auth::check() && Auth::user()->role === 'Bailleur') {
            return $next($request);
        } else {
            return response()->json([
                "message" => "vous n'êtes pas Connecté en tant que Bailleur"
            ]);
        }
    }
}
