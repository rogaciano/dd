<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Denuncia;
use App\Models\VeiculoMarca;
use App\Models\VeiculoModelo;
use App\Models\DenunciaVeiculo;
use Illuminate\Support\Facades\Schema;

#[Signature('legado:importar-veiculos')]
#[Description('Importa veículos da base legada.')]
class ImportLegadoVeiculos extends Command
{
    public function handle()
    {
        $this->info("Iniciando importação de Veículos...");

        $dbLegado = DB::connection('pgsql_legado');

        try {
            DB::statement('SET session_replication_role = replica;');
            
            $this->info("Importando marcas...");
            $marcas = $dbLegado->table('vei_marca')->get();
            $marcaMap = []; 
            
            foreach ($marcas as $m) {
                $marca = VeiculoMarca::updateOrCreate(
                    ['nome' => trim($m->mar_ds)]
                );
                $marcaMap[$m->mar_cd] = $marca->id;
            }

            $this->info("Importando modelos...");
            $modeloMap = [];
            if (Schema::connection('pgsql_legado')->hasTable('vei_modelo')) {
                $modelos = $dbLegado->table('vei_modelo')->get();
                foreach ($modelos as $mo) {
                    $modelo = VeiculoModelo::updateOrCreate(
                        ['nome' => trim($mo->mod_ds)],
                        ['veiculo_marca_id' => $marcaMap[$mo->mod_mar_cd] ?? null]
                    );
                    $modeloMap[$mo->mod_cd] = $modelo->id;
                }
            }

            $this->info("Importando veículos...");
            $veiculos = $dbLegado->table('veiculos')->get();
            $count = 0;

            foreach ($veiculos as $v) {
                $denuncia = Denuncia::where('id', $v->den_cd)->first(); 

                if ($denuncia) {
                    DenunciaVeiculo::updateOrCreate(
                        [
                            'denuncia_id' => $denuncia->id,
                            'placa' => trim($v->placa), 
                        ],
                        [
                            'veiculo_marca_id' => $marcaMap[$v->marca] ?? null,
                            'veiculo_modelo_id' => $modeloMap[$v->modelo ?? ''] ?? null,
                            'cor' => trim($v->cor),
                            'ano_modelo' => (int) $v->ano_mod ?: null,
                            'ano_fabricacao' => (int) $v->ano_fab ?: null,
                            'chassis' => trim($v->chassis),
                            'municipio' => trim($v->municipio),
                            'uf' => trim($v->uf),
                            'proprietario' => trim($v->proprietario),
                            'detalhes' => trim($v->detalhes),
                        ]
                    );
                    $count++;
                }
            }

            DB::statement('SET session_replication_role = DEFAULT;');
            $this->info("Importação de veículos finalizada! {$count} veículos importados.");
            
        } catch (\Exception $e) {
            DB::statement('SET session_replication_role = DEFAULT;');
            $this->error("Erro de importação: " . $e->getMessage());
        }
    }
}
