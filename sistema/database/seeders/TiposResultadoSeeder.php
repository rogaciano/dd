<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\TipoResultado;

class TiposResultadoSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            'Prisão Efetuada',
            'Apreensão de Entorpecentes',
            'Apreensão de Armas',
            'Foragido Recapturado',
            'Veículo Recuperado',
            'Multa Aplicada',
            'Denúncia Infundada',
            'Sem Resultado Positivo'
        ];

        foreach ($tipos as $i => $tipo) {
            TipoResultado::firstOrCreate(
                ['slug' => Str::slug($tipo)],
                ['nome' => $tipo, 'ordem_exibicao' => $i]
            );
        }
    }
}
