<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'document_number' => 'required|string',
            'password' => 'required'
        ]);

        // Intentar autenticar con el campo personalizado
        if (!Auth::attempt(['document_number' => $credentials['document_number'], 'password' => $credentials['password']])) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $user = $request->user();
        // Cargar la relación con su casilla para que el frontend sepa a qué buzón entrar
        $user->load('casilla');

        return response()->json([
            'token' => $user->createToken('auth_token')->plainTextToken,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }
}
