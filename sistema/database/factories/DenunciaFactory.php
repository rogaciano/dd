<?php

namespace Database\Factories;

use App\Support\DenunciaCanal;
use App\Support\DenunciaStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DenunciaFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement([
            DenunciaStatus::RECEBIDA,
            DenunciaStatus::TRIAGEM,
            DenunciaStatus::EM_ANDAMENTO,
            DenunciaStatus::ENCERRADA,
        ]);

        return [
            'token_acompanhamento_hash' => hash('sha256', Str::random(32)),
            'canal' => DenunciaCanal::WEB,
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
