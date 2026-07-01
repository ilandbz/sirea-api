<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Casilla;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'document_number' => 'required|unique:users,document_number',
            'document_type' => 'required|in:DNI,RUC',
            'password' => 'required|min:6'
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'document_number' => $request->document_number,
                'document_type' => $request->document_type,
                'role' => 'usuario_final', // Rol por defecto (cliente final)
                'is_active' => true,
                'password' => Hash::make($request->password),
            ]);

            $user->casilla()->create([
                'numero_casilla' => 'SIREA-' . date('Y') . '-' . str_pad(Casilla::count() + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'activa'
            ]);

            return response()->json([
                'message' => 'Cuenta creada con éxito',
                'token' => $user->createToken('auth_token')->plainTextToken,
                'user' => $user->load('casilla')
            ], 201);
        });
    }

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
