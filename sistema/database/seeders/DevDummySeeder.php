<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Denuncia;
use App\Models\DenunciaLocal;

class DevDummySeeder extends Seeder
{
    public function run()
    {
        if (app()->environment('local')) {
            $this->command->info('Ambiente Local detectado. Semeando dados ficticios de validacao...');

            Denuncia::factory(30)->create()->each(function ($denuncia) {
                // Cria locais para as denuncias geradas
                DenunciaLocal::factory()->create([
                    'denuncia_id' => $denuncia->id
                ]);
                
                // Associa a um assunto aleatorio se houver no banco mestre
                $assuntos = \App\Models\Assunto::inRandomOrder()->limit(1)->pluck('id')->toArray();
                if(!empty($assuntos)){
                    $denuncia->assuntos()->attach($assuntos, ['principal' => true]);
                }
            });

            $this->command->info('30 Denuncias de teste criadas!');
        }
    }
}
