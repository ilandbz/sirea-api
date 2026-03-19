<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Casilla extends Model
{
    protected $fillable = [
        'user_id',
        'numero_casilla',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
