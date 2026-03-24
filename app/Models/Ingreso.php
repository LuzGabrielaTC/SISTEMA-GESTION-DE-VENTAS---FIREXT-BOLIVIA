<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingreso extends Model
{
    use HasFactory;

    protected $table = 'ingreso';
    protected $primaryKey = 'id_ingreso';

    protected $fillable = [
        'id_recepcion',
        'id_entrega',
        'tipo_pago',
        'monto',
        'estado'
    ];

    protected $casts = [
        'monto' => 'float',
        'estado' => 'boolean'
    ];

    public function notaRecepcion()
    {
        return $this->belongsTo(NotaRecepcion::class, 'id_recepcion', 'id_recepcion');
    }
    public function notaEntrega()
    {
        return $this->belongsTo(NotaEntrega::class, 'id_entrega', 'id_entrega');
    }
    
}
