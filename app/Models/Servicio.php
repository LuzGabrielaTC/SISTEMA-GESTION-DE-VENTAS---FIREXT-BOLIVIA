<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $table = 'servicio';
    protected $primaryKey = 'id_item_servicio';
    public $incrementing = false;

    protected $fillable = [
        'id_item_servicio',
        'tipo_gas'
    ];

    /**
     * Relación inversa: El servicio pertenece a un Item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item_servicio', 'id_item');
    }
}