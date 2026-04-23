<?php

namespace App\Console\Commands;

use App\Models\Denuncia;
use App\Models\DenunciaVeiculo;
use App\Models\VeiculoMarca;
use App\Models\VeiculoModelo;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Signature('legado:importar-veiculos')]
#[Description('Importa veiculos da base legada.')]
class ImportLegadoVeiculos extends Command
{
    public function handle()
    {
        $this->info('Iniciando importacao de veiculos...');

        $dbLegado = DB::connection('pgsql_legado');
        $dbNovo = DB::connection();

        try {
            if ($dbNovo->getDriverName() === 'pgsql') {
                $dbNovo->statement('SET session_replication_role = replica;');
            }

            $marcaMap = $this->importarMarcas($dbLegado);
            $modeloMap = $this->importarModelos($dbLegado, $marcaMap);

            $count = 0;
            $veiculos = $dbLegado->table('veiculos')->get();

            foreach ($veiculos as $veiculoLegado) {
                $denuncia = Denuncia::query()
                    ->where('origem_legado_tabela', 'denuncia')
                    ->where('origem_legado_id', $veiculoLegado->den_cd)
                    ->first();

                if (! $denuncia) {
                    continue;
                }

                DenunciaVeiculo::updateOrCreate(
                    [
                        'denuncia_id' => $denuncia->id,
                        'placa' => $this->nullableTrim($veiculoLegado->placa),
                    ],
                    [
                        'veiculo_marca_id' => $marcaMap[$veiculoLegado->marca] ?? null,
                        'veiculo_modelo_id' => $modeloMap[$veiculoLegado->modelo] ?? null,
                        'cor' => $this->nullableTrim($veiculoLegado->cor),
                        'ano_modelo' => $this->nullableInt($veiculoLegado->ano_mod),
                        'ano_fabricacao' => $this->nullableInt($veiculoLegado->ano_fab),
                        'chassis' => $this->nullableTrim($veiculoLegado->chassis),
                        'municipio' => $this->nullableTrim($veiculoLegado->municipio),
                        'uf' => $this->nullableTrim($veiculoLegado->uf),
                        'proprietario' => $this->nullableTrim($veiculoLegado->proprietario),
                        'detalhes' => $this->nullableTrim($veiculoLegado->detalhes),
                    ]
                );

                $count++;
            }

            $this->info("Importacao de veiculos finalizada! {$count} veiculos importados.");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Erro de importacao: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            if ($dbNovo->getDriverName() === 'pgsql') {
                $dbNovo->statement('SET session_replication_role = DEFAULT;');
            }
        }
    }

    private function importarMarcas($dbLegado): array
    {
        $this->info('Importando marcas...');

        $marcaMap = [];

        foreach ($dbLegado->table('vei_marca')->get() as $marcaLegada) {
            $nomeMarca = $this->nullableTrim($marcaLegada->mar_ds);

            if (! $nomeMarca) {
                continue;
            }

            $marca = VeiculoMarca::updateOrCreate([
                'nome' => $nomeMarca,
            ]);

            $marcaMap[$marcaLegada->mar_cd] = $marca->id;
        }

        return $marcaMap;
    }

    private function importarModelos($dbLegado, array $marcaMap): array
    {
        $this->info('Importando modelos...');

        $modeloMap = [];

        if (! Schema::connection('pgsql_legado')->hasTable('vei_modelo')) {
            return $modeloMap;
        }

        foreach ($dbLegado->table('vei_modelo')->get() as $modeloLegado) {
            $nomeModelo = $this->nullableTrim($modeloLegado->mod_ds);

            if (! $nomeModelo) {
                continue;
            }

            $modelo = VeiculoModelo::updateOrCreate(
                ['nome' => $nomeModelo],
                ['veiculo_marca_id' => $marcaMap[$modeloLegado->mar_cd] ?? null],
            );

            $modeloMap[$modeloLegado->mod_cd] = $modelo->id;
        }

        return $modeloMap;
    }

    private function nullableInt(mixed $value): ?int
    {
        $value = (int) $value;

        return $value > 0 ? $value : null;
    }

    private function nullableTrim(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
