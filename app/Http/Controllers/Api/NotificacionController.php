<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $notificaciones = Notificacion::where('receptor_id', $request->user()->id)
            ->with(['expediente', 'adjuntos'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notificaciones);
    }
    public function show($id)
    {
        $notificacion = Notificacion::findOrFail($id);

        if (!$notificacion->fecha_lectura) {
            $notificacion->update([
                'fecha_lectura' => now(),
                'ip_lectura' => request()->ip()
            ]);

            // Registrar en tabla de auditoría
            AuditLog::create([
                'user_id' => auth()->id(),
                'notificacion_id' => $id,
                'accion' => 'LECTURA_CASILLA',
                'metadata' => json_encode(['ua' => request()->userAgent()])
            ]);
        }

        return new NotificacionResource($notificacion->load('adjuntos'));
    }
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'expediente_id' => 'required|exists:expedientes,id',
    //         'asunto'        => 'required|string',
    //         'cuerpo'        => 'required|string',
    //         'archivos.*'    => 'required|mimes:pdf|max:10240',
    //     ]);

    //     return DB::transaction(function () use ($request, $validated) {
    //         // 1. Obtener todos los usuarios vinculados al expediente
    //         $expediente = Expediente::with('usuarios')->find($validated['expediente_id']);

    //         $respuestas = [];

    //         foreach ($expediente->usuarios as $usuario) {
    //             // 2. Crear la notificación para CADA usuario vinculado
    //             $notificacion = Notificacion::create([
    //                 'expediente_id'      => $expediente->id,
    //                 'receptor_id'        => $usuario->id, // El usuario recibe en su casilla
    //                 'emisor_id'          => auth()->id(),
    //                 'asunto'             => $validated['asunto'],
    //                 'cuerpo'             => $validated['cuerpo'],
    //                 'codigo_seguimiento' => 'NOT-' . time() . '-' . $usuario->id,
    //                 'fecha_deposito'     => now(),
    //             ]);

    //             // 3. Guardar archivos para esta notificación
    //             if ($request->hasFile('archivos')) {
    //                 foreach ($request->file('archivos') as $file) {
    //                     $path = $file->store('notificaciones/' . $notificacion->id);
    //                     $notificacion->adjuntos()->create([
    //                         'nombre_archivo' => $file->getClientOriginalName(),
    //                         'ruta_storage'   => $path,
    //                     ]);
    //                 }
    //             }
    //             $respuestas[] = $notificacion;
    //         }

    //         return response()->json(['message' => 'Notificación enviada a todos los involucrados', 'data' => $respuestas]);
    //     });
    // }

    public function store(Request $request)
    {
        $request->validate([
            'expediente_id' => 'required|exists:expedientes,id',
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string',
            'archivos.*' => 'required|mimes:pdf|max:20480', // Máx 20MB por PDF
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Buscamos el expediente con sus usuarios vinculados
            $expediente = Expediente::with('usuarios')->findOrFail($request->expediente_id);

            if ($expediente->usuarios->isEmpty()) {
                return response()->json(['message' => 'El expediente no tiene usuarios vinculados.'], 422);
            }

            $notificacionesCreadas = [];

            // 2. Por cada usuario vinculado, creamos una notificación en su casilla
            foreach ($expediente->usuarios as $usuario) {
                $notificacion = Notificacion::create([
                    'expediente_id' => $expediente->id,
                    'receptor_id' => $usuario->id,
                    'emisor_id' => $request->user()->id,
                    'asunto' => $request->asunto,
                    'cuerpo' => $request->mensaje,
                    'codigo_seguimiento' => 'NOT-' . strtoupper(uniqid()),
                    'fecha_deposito' => now(),
                ]);

                // 3. Guardamos los archivos adjuntos
                if ($request->hasFile('archivos')) {
                    foreach ($request->file('archivos') as $file) {
                        $path = $file->store('notificaciones/' . $notificacion->id, 'public');
                        $notificacion->adjuntos()->create([
                            'nombre_archivo' => $file->getClientOriginalName(),
                            'ruta_storage' => $path,
                        ]);
                    }
                }
                $notificacionesCreadas[] = $notificacion->id;
            }

            return response()->json([
                'message' => 'Notificación depositada con éxito en ' . count($notificacionesCreadas) . ' casillas.',
                'ids' => $notificacionesCreadas
            ]);
        });
    }
}
