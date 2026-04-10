<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DenunciaFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['recebida', 'triagem', 'em_andamento', 'encerrada']);
        $protocolo = Str::upper($this->faker->bothify('?##')). '.' . str_pad($this->faker->numberBetween(1, 12), 2, '0', STR_PAD_LEFT) . '.2026';
        
        return [
            'protocolo' => $protocolo,
            'token_acompanhamento_hash' => hash('sha256', Str::random(32)),
            'canal' => 'web',
            'status' => $status,
            'prioridade' => $this->faker->randomElement(['normal', 'alta']),
            'urgente' => $this->faker->boolean(20),
            'resumo' => $this->faker->sentence(10),
            'relato' => $this->faker->paragraphs(3, true),
            'recebida_em' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'enviada_em' => now(),
            'ip_hash' => hash('sha256', $this->faker->ipv4),
            'user_agent_hash' => hash('sha256', $this->faker->userAgent),
        ];
    }
}
