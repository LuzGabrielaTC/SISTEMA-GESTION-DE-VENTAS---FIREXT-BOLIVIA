<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Producto;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $nombres = [
            'Gabinete Metálico Estándar',
            'Soporte de Pared Universal',
            'Manguera de Alta Presión 1/2',
            'Válvula de Bronce Cromado',
            'Manómetro de Control 195 PSI',
            'Señalética Fotoluminiscente'
        ];

        return [
            // Crea un Item y usa su ID para el producto
            'id_item_producto' => Item::factory(),
            'nombre' => $this->faker->randomElement($nombres),
        ];
    }
}