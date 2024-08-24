<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticateAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return redirect()->route('user.login');
            }

            if ($user->isPerson() !== 'user') {
                return response()->json(['error' => 'Acceso no autorizado'], 403);
            }

        } catch (JWTException $e) {
            return redirect()->route('user.login');
        }

        return $next($request);
    }
   
}
