<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
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
            'ci' => $this->faker->unique()->numberBetween(1000000, 9999999),
            'telefono' => $this->faker->phoneNumber(),
            'rol'=> $this->faker->randomElement(['Administrador','Vendedor']),
            'username' => $this->faker->unique()->userName(),
            // Usamos Hash::make o bcrypt para que Laravel pueda validar la sesión luego
            'password' => \Illuminate\Support\Facades\Hash::make('123456'),
        ];
    }
}
