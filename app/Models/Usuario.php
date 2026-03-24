<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'apellido',
        'ci',
        'telefono',
        'rol',
        'username',
        'password',
        'estado'
    ];

    // Ocultar la contraseña en las respuestas JSON o consultas
    protected $hidden = [
        'password',
    ];

    // Casts para manejar la encriptación de la contraseña automáticamente (opcional en Laravel 10+)
    protected $casts = [
        'estado' => 'boolean',
        'password' => 'hashed',
    ];
}