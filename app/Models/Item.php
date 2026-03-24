<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'item';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_recepcion',
        'id_entrega',
        'marca',
        'articulo',
        'capacidad',
        'unidad',
        'serie',
        'precio',
        'descripcion',
        'estado'
    ];

    // Casts para asegurar tipos de datos correctos en React
    protected $casts = [
        'capacidad' => 'float',
        'precio' => 'float',
        'estado' => 'boolean',
    ];

    /**
     * RELACIONES CON LAS NOTAS
     */

    public function recepcion()
    {
        return $this->belongsTo(NotaRecepcion::class, 'id_recepcion', 'id_recepcion');
    }

    public function entrega()
    {
        return $this->belongsTo(NotaEntrega::class, 'id_entrega', 'id_entrega');
    }

    /**
     * RELACIONES DE HERENCIA (Uno a Uno)
     */

    // Si el item es un servicio (Recarga/Mantenimiento)
    public function servicio()
    {
        return $this->hasOne(Servicio::class, 'id_item_servicio', 'id_item');
    }

    // Si el item es un producto (Venta de accesorios/extintores nuevos)
    public function producto()
    {
        return $this->hasOne(Producto::class, 'id_item_producto', 'id_item');
    }
}