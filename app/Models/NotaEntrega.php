<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotaEntrega extends Model
{
    use HasFactory;

    protected $table = 'nota_entrega';
    protected $primaryKey = 'id_entrega';

    protected $fillable = [
        'id_recepcion',
        'id_usuario',
        'fecha',
        'cantidad',
        'precio_total',
        'a_cuenta',
        'saldo',
        'tipo_pago',
        'observacion',
        'tipoEntrega',
        'estado'
    ];

    protected $casts = [
        'precio_total' => 'float',
        'a_cuenta' => 'float',
        'saldo' => 'float',
        'fecha' => 'date',
        'estado' => 'boolean'
    ];

    public function recepcion()
    {
        return $this->belongsTo(NotaRecepcion::class, 'id_recepcion', 'id_recepcion');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
