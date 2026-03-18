<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;

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
}
