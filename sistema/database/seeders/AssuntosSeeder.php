<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Assunto;
use App\Models\GrupoAssunto;

class AssuntosSeeder extends Seeder
{
    public function run()
    {
        $mapa = [
            'Crimes contra a Pessoa' => ['Homicídio', 'Agressão', 'Feminicídio', 'Sequestro', 'Ameaça'],
            'Crimes Patrimoniais' => ['Furto', 'Roubo a Transeunte', 'Roubo de Veículo', 'Estelionato', 'Extorsão'],
            'Crimes Ambientais e Maus Tratos' => ['Maus Tratos a Animais', 'Desmatamento', 'Poluição', 'Caça Ilegal'],
            'Tráfico e Entorpecentes' => ['Tráfico de Drogas', 'Ponto de Venda (Boca de Fumo)', 'Cultivo Ilegal'],
            'Crimes de Trânsito' => ['Direção Perigosa', 'Embriaguez ao Volante', 'Racha'],
            'Outros' => ['Atitude Suspeita', 'Informação Geral', 'Vandalismo']
        ];

        foreach ($mapa as $grupoNome => $assuntos) {
            $grupo = GrupoAssunto::where('slug', Str::slug($grupoNome))->first();
            if ($grupo) {
                foreach ($assuntos as $i => $assunto) {
                    Assunto::firstOrCreate(
                        ['slug' => Str::slug($assunto)],
                        ['nome' => $assunto, 'grupo_assunto_id' => $grupo->id, 'ordem_exibicao' => $i]
                    );
                }
            }
        }
    }
}
