<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    public function index()
    {
        // Usamos 'with' para cargar la relación 'usuarios' y el campo 'tipo_parte' del pivot
        $expedientes = Expediente::with(['usuarios' => function ($query) {
            $query->select('users.id', 'users.name', 'users.document_number')
                ->withPivot('tipo_parte'); // Cargamos el tipo de parte (DEMANDANTE, etc.)
        }])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($expedientes);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|unique:expedientes',
            'tipo' => 'required|in:IA,JRD,CONCILIACION',
            'materia' => 'required',
            'demandante' => 'required',
            'demandado' => 'required',
        ]);

        $expediente = Expediente::create($validated);
        return response()->json($expediente, 201);
    }

    public function vincularUsuario(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipo_parte' => 'required|in:DEMANDANTE,DEMANDADO,ARBITRO'
        ]);

        $expediente = Expediente::findOrFail($id);

        // attach() inserta en la tabla intermedia expediente_user
        $expediente->usuarios()->attach($request->user_id, [
            'tipo_parte' => $request->tipo_parte
        ]);

        return response()->json(['message' => 'Usuario vinculado correctamente']);
    }

    public function buscar(Request $request)
    {
        $term = $request->query('q');
        // Busca por número de expediente o nombre de demandante
        return Expediente::where('codigo', 'LIKE', "%$term%")
            ->orWhere('demandante', 'LIKE', "%$term%")
            ->limit(10)
            ->get();
    }
}
