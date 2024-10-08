<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\UserRole;

class AuthController extends Controller
{
    public function __construct()
    {
        // Aplica el middleware de autenticación a todos los métodos excepto 'login'
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'rememberMe' => 'boolean'
        ]);

        $credentials = $request->only(['email', 'password']);
        $remember = $request->input('rememberMe', false);

        try {
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Credenciales inválidas'], 401);
            }

            $user = Auth::user();
            $userRoles = UserRole::where('user_id', $user->id)->get();
            if ($userRoles->isEmpty()) {
                return response()->json(['error' => 'Este usuario no tiene roles asignados'], 401);
            }

            $ttl = $remember ? 43200 : 3600; // 12 horas o 1 hora
            auth()->setTTL($ttl);

            return response()->json([
                'token' => $token,
                'message' => 'Usuario autenticado'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno'], 500);
        }
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['mensaje' => 'Cierre de sesión exitoso']);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
