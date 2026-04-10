<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\GrupoAssunto;

class GruposAssuntoSeeder extends Seeder
{
    public function run()
    {
        $grupos = [
            'Crimes contra a Pessoa',
            'Crimes Patrimoniais',
            'Crimes Ambientais e Maus Tratos',
            'Tráfico e Entorpecentes',
            'Crimes de Trânsito',
            'Outros'
        ];

        foreach ($grupos as $i => $grupo) {
            GrupoAssunto::firstOrCreate(
                ['slug' => Str::slug($grupo)],
                ['nome' => $grupo, 'ordem_exibicao' => $i]
            );
        }
    }
}
