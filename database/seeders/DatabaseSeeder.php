<?php

namespace Database\Seeders;

use App\Models\User;

use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\NotaRecepcion;
use App\Models\NotaEntrega;
use App\Models\Item;
use App\Models\Producto;
use App\Models\Servicio;
use App\Models\Egreso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Usuario::factory()->create([
            "nombre"=> "Kae",
            "apellido"=> "Reyes",
            "ci"=> "999999",
            "telefono"=> "7889889",
            "rol"=> "Admin",
            "username"=> "KaeReyes",
            "password"=> "password123"
        ]);

        Usuario::factory(20)->create();
        Cliente::factory(100)->create();

        NotaRecepcion::factory(100)->create();
        NotaEntrega::factory(100)->create();

        Servicio::factory(100)->create();
        Producto::factory(100)->create();

        Egreso::factory(100)->create();
    }
}
