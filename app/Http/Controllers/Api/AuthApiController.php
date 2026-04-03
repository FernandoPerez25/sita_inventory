<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    // ── POST /api/login ──────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Aceptar login con username o email
        $campo = filter_var($request->username, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $usuario = Usuario::where($campo, $request->username)->first();

        if (!$usuario || !Hash::check($request->password, $usuario->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        if (!$usuario->activo) {
            return response()->json([
                'message' => 'Tu cuenta está desactivada. Contacta al administrador.',
            ], 403);
        }

        // Cargar rol para devolverlo en la respuesta
        $usuario->load('rol');

        // Crear token Sanctum con nombre del dispositivo
        $token = $usuario->createToken(
            $request->input('device_name', 'app-movil')
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => [
                'id'             => $usuario->id,
                'nombre_completo' => $usuario->nombre_completo,
                'username'       => $usuario->username,
                'email'          => $usuario->email,
                'rol'            => $usuario->rol->nombre,
                'puede_editar'   => $usuario->puedeEditar(),
            ],
        ]);
    }

    // ── POST /api/logout ─────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        // Revocar solo el token actual
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    // ── GET /api/me ──────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = $request->user();
        $usuario->load('rol');

        return response()->json([
            'id'             => $usuario->id,
            'nombre_completo' => $usuario->nombre_completo,
            'username'       => $usuario->username,
            'email'          => $usuario->email,
            'rol'            => $usuario->rol->nombre,
            'puede_editar'   => $usuario->puedeEditar(),
        ]);
    }
}
