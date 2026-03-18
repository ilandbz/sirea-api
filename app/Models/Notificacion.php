<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $fillable = [
        'codigo_seguimiento',
        'expediente_id',
        'receptor_id',
        'emisor_id',
        'asunto',
        'cuerpo',
        'fecha_deposito',
        'fecha_lectura',
        'ip_lectura',
    ];

    public function adjuntos()
    {
        return $this->hasMany(NotificacionAdjunto::class);
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
    public function receptor()
    {
        return $this->belongsTo(User::class, 'receptor_id');
    }
    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }
}
