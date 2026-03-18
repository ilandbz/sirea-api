<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $table = 'expedientes';


    protected $fillable = ['codigo', 'tipo', 'materia', 'demandante', 'demandado', 'estado'];
    const TIPO_ARBITRAJE = 'IA';
    const TIPO_JRD = 'JRD';
    const TIPO_CONCILIACION = 'CONCILIACION';

    public static function getTipos()
    {
        return [
            self::TIPO_ARBITRAJE => 'Expediente de Arbitraje (IA)',
            self::TIPO_JRD => 'Junta de Resolución de Disputas (JRD)',
            self::TIPO_CONCILIACION => 'Expediente de Conciliación',
        ];
    }
}
