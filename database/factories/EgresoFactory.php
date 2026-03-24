<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Egreso>
 */
class EgresoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_usuario' => Usuario::inRandomOrder()->first()->id_usuario ?? Usuario::factory(),
            'tipo'       => $this->faker->randomElement(['Compra', 'Sueldo', 'Otros']),
            'monto'      => $this->faker->randomFloat(2, 0, 1000),
            'descripcion' => $this->faker->sentence(),
            'estado'     => true,
        ];
    }
}
