<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PorteurMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (auth()->check()) {

            $user = auth()->user();

            if ($user->role === 'Porteur') {
                return $next($request);
            }
        } else {
            return response()->json([
                "message" => "vous n'êtes pas Connecté en tant que Porteur de projet"
            ]);
        }
    }
}
