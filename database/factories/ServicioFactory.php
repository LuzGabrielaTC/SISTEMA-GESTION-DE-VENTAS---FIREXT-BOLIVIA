<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Servicio;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicioFactory extends Factory
{
    protected $model = Servicio::class;

    public function definition(): array
    {
        $gases = ['PQS (Fosfato Monoamónico)', 'CO2 (Dióxido de Carbono)', 'Agua Presurizada', 'Espuma AFFF', 'HCFC-123'];

        return [
            // Crea un Item y usa su ID para el servicio
            'id_item_servicio' => Item::factory(), 
            'tipo_gas' => $this->faker->randomElement($gases),
        ];
    }
}