<?php

namespace Database\Factories;

use App\Models\NotaRecepcion;
use App\Models\NotaEntrega;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ingreso>
 */
class IngresoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_recepcion' => NotaRecepcion::inRandomOrder()->first()->id_recepcion ?? NotaRecepcion::factory(),
            'id_entrega'   => NotaEntrega::inRandomOrder()->first()->id_entrega ?? NotaEntrega::factory(),
            'tipo_pago'   => $this->faker->randomElement(['Efectivo', 'QR', 'Transferencia']),
            'monto'       => $this->faker->randomFloat(2, 0, 1000),
            'estado'      => true,
        ];
    }
}
