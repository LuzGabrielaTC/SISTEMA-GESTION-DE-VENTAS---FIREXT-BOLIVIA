<?php

namespace Database\Factories;

use App\Models\NotaRecepcion;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotaEntregaFactory extends Factory
{
    public function definition(): array
    {
        $precioTotal = $this->faker->randomFloat(2, 100, 2000);
        $aCuenta = $this->faker->randomFloat(2, 0, $precioTotal);
        $saldo = $precioTotal - $aCuenta;

        return [
            'id_recepcion' => NotaRecepcion::inRandomOrder()->first()->id_recepcion ?? NotaRecepcion::factory(),
            'id_usuario'   => Usuario::inRandomOrder()->first()->id_usuario ?? Usuario::factory(),
            
            'fecha'        => $this->faker->date(),
            'cantidad'     => $this->faker->numberBetween(1, 10),
            'precio_total' => $precioTotal,
            'a_cuenta'     => $aCuenta,
            'saldo'        => $saldo,
            'observacion'  => $this->faker->sentence(),
            'tipoEntrega'  => $this->faker->randomElement(['En tienda', 'Mobil']),
            'tipo_pago'    => $this->faker->randomElement(['Efectivo', 'QR', 'Transferencia']),
            'estado'       => true,
        ];
    }
}