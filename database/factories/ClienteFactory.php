<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'razon_social' => $this->faker->company(),
            'nit' => $this->faker->numerify('###########'), // Genera un NIT de 11 dígitos
            'ci' => $this->faker->unique()->numberBetween(1000000, 9999999),
            'telefono' => $this->faker->phoneNumber(),
        ];
    }
}
