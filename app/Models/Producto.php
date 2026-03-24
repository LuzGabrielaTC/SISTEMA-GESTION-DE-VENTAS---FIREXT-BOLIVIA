<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'producto';
    
    protected $primaryKey = 'id_item_producto';
    public $incrementing = false;

    protected $fillable = [
        'id_item_producto',
        'nombre'
    ];

    /**
     * Relación inversa: El producto pertenece a un Item.
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'id_item_producto', 'id_item');
    }
}