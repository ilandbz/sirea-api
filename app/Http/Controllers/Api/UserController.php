<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Casilla;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('casilla')->orderBy('id', 'desc')->get();
        return response()->json($users);
    }

    public function registrarUsuario(Request $request)
    {
        // Validar datos según tu modelo
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'document_number' => 'required|unique:users,document_number',
            'document_type' => 'required|in:DNI,RUC',
            'role' => 'required'
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Crear el Usuario usando los campos de tu modelo
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'document_number' => $request->document_number,
                'document_type' => $request->document_type,
                'role' => $request->role,
                'is_active' => true,
                'password' => Hash::make($request->document_number), // Clave inicial = documento
            ]);

            // 2. Generar Casilla Automática
            $user->casilla()->create([
                'numero_casilla' => 'SIREA-' . date('Y') . '-' . str_pad(Casilla::count() + 1, 4, '0', STR_PAD_LEFT),
                'status' => 'activa'
            ]);

            return response()->json(['message' => 'Usuario y Casilla creados con éxito'], 201);
        });
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email', 'role', 'is_active']));
        return response()->json(['message' => 'Usuario actualizado']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Opcional: Validar si tiene notificaciones antes de borrar
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }
}
