<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CargaNotificacionController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validar la entrada
        $request->validate([
            'expediente_id' => 'required|exists:expedientes,id',
            'casilla_id'    => 'required|exists:casillas,id',
            'asunto'        => 'required|string|max:255',
            'mensaje'       => 'required',
            'archivos.*'    => 'required|mimes:pdf,doc,docx|max:10240', // Máx 10MB
        ]);

        return DB::transaction(function () use ($request) {
            // 2. Crear la notificación (El "Depósito")
            $notificacion = Notificacion::create([
                'expediente_id'   => $request->expediente_id,
                'receptor_id'     => $request->casilla_id,
                'emisor_id'       => auth()->id(),
                'asunto'          => $request->asunto,
                'cuerpo'          => $request->mensaje,
                'codigo_seguimiento' => 'NOT-' . time(),
                'fecha_deposito'  => now(),
            ]);

            // 3. Subir archivos
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $file) {
                    $path = $file->store('expedientes/' . $request->expediente_id);
                    $notificacion->adjuntos()->create([
                        'nombre_archivo' => $file->getClientOriginalName(),
                        'ruta_storage'   => $path,
                    ]);
                }
            }

            return response()->json(['message' => 'Notificación enviada con éxito', 'id' => $notificacion->id], 201);
        });
    }
}
