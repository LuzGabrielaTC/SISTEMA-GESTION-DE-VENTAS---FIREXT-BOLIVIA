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

        Usuario::factory(3)->create();
        Cliente::factory(10)->create();

        NotaRecepcion::factory(5)->create();
        NotaEntrega::factory(3)->create();

        Servicio::factory(5)->create();
        Producto::factory(5)->create();

        Egreso::factory(5)->create();
    }
}
