<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DenunciaLocalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pais_codigo' => 'BR',
            'uf' => 'RJ',
            'municipio' => 'Rio de Janeiro',
            'bairro' => $this->faker->word(),
            'logradouro_nome' => $this->faker->streetName(),
            'numero' => $this->faker->buildingNumber(),
            'cep' => $this->faker->postcode(),
            'referencia' => $this->faker->sentence(),
            'latitude' => $this->faker->latitude(-23, -22),
            'longitude' => $this->faker->longitude(-43, -42),
        ];
    }
}
