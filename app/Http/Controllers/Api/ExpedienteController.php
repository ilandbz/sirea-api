<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Expediente::query();
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        return response()->json($query->orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|unique:expedientes',
            'tipo' => 'required|in:IA,JRD,CONCILIACION',
            'materia' => 'required',
            'demandante' => 'required',
            'demandado' => 'required',
            'fecha_inicio' => 'required|date',
        ]);

        $expediente = Expediente::create($data);
        return response()->json($expediente, 201);
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
