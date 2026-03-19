<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionAdjunto extends Model
{
    protected $fillable = [
        'notificacion_id',
        'nombre_archivo',
        'ruta_storage'
    ];

    public function notificacion()
    {
        return $this->belongsTo(Notificacion::class);
    }
}
