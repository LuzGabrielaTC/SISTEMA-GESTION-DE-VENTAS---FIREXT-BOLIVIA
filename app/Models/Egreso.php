<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Egreso extends Model
{
    use HasFactory;

    protected $table = 'egreso';
    protected $primaryKey = 'id_egreso';

    protected $fillable = [
        'id_usuario',
        'tipo',
        'monto',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'monto' => 'float',
        'estado' => 'boolean'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
