<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotaRecepcionFactory extends Factory
{
    public function definition(): array
    {
        $precioTotal = $this->faker->randomFloat(2, 100, 1000); // Precio entre 100 y 1000
        $aCuenta = $this->faker->randomFloat(2, 0, $precioTotal); // Pago parcial
        $saldo = $precioTotal - $aCuenta;

        return [
            'id_cliente'   => Cliente::inRandomOrder()->first()->id_cliente ?? Cliente::factory(),
            'id_usuario'   => Usuario::inRandomOrder()->first()->id_usuario ?? Usuario::factory(),
            
            'fecha'        => $this->faker->date(),
            'cantidad'     => $this->faker->numberBetween(1, 20),
            'precio_total' => $precioTotal,
            'a_cuenta'     => $aCuenta,
            'saldo'        => $saldo,
            'observacion'  => $this->faker->sentence(),
            'tipoReserva'  => $this->faker->randomElement(['En tienda', 'Mobil']),
            'tipo_pago'    => $this->faker->randomElement(['Efectivo', 'QR', 'Transferencia']),
            'estado'       => true,
        ];
    }
}