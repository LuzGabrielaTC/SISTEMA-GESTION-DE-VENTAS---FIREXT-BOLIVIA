<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotaRecepcion extends Model
{
    use HasFactory;

    protected $table = 'nota_recepcion';
    protected $primaryKey = 'id_recepcion';

    protected $fillable = [
        'id_cliente',
        'id_usuario',
        'fecha',
        'cantidad',
        'precio_total',
        'a_cuenta',
        'saldo',
        'tipo_pago',
        'observacion',
        'tipoReserva',
        'estado'
    ];

    protected $casts = [
        'precio_total' => 'float',
        'a_cuenta' => 'float',
        'saldo' => 'float',
        'fecha' => 'date',
        'estado' => 'boolean'
    ];
    /**
     * Relación: Una Nota de Recepción pertenece a un Cliente.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    /**
     * Relación: Una Nota de Recepción pertenece a un Usuario (el que la registró).
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relación: Una Nota de Recepción tiene muchos Items (extintores).
     * (Esta la usaremos cuando crees la tabla de Items)
     */
    // public function items()
    // {
    //     return $this->hasMany(Item::class, 'id_recepcion', 'id_recepcion');
    // }
}
