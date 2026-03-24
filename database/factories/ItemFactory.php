<?php

namespace Database\Factories;

use App\Models\NotaRecepcion;
use App\Models\NotaEntrega;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition(): array
    {
        $marcas = ['Babcock', 'Amerex', 'Badger', 'Buckeye', 'Kidde'];
        $articulos = ['Extintor PQS', 'Extintor CO2', 'Extintor de Agua', 'Extintor de Espuma'];
        $unidades = ['kg', 'lb'];
        $capacidades = [1, 2, 4, 6, 9, 12];

        return [
            'id_recepcion' => NotaRecepcion::inRandomOrder()->first()->id_recepcion ?? NotaRecepcion::factory(),
            
            'id_entrega'   => $this->faker->randomElement([null, NotaEntrega::inRandomOrder()->first()->id_entrega ?? null]),
            
            'marca'        => $this->faker->randomElement($marcas),
            'articulo'     => $this->faker->randomElement($articulos),
            'capacidad'    => $this->faker->randomElement($capacidades),
            'unidad'       => $this->faker->randomElement($unidades),
            'serie'        => $this->faker->bothify('EXT-####-??'), // Ejemplo: EXT-1234-AB
            'precio'       => $this->faker->randomFloat(2, 50, 500),
            'descripcion'  => $this->faker->sentence(),
            'estado'       => true,
        ];
    }
}