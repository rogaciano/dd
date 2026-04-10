<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Papel;

class PapeisSeeder extends Seeder
{
    public function run()
    {
        $papeis = [
            'Administrador',
            'Supervisor',
            'Analista',
            'Atendente',
            'Visualizador'
        ];

        foreach ($papeis as $papel) {
            Papel::firstOrCreate(
                ['slug' => Str::slug($papel)],
                ['nome' => $papel]
            );
        }
    }
}
