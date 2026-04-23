<?php

namespace App\Console\Commands;

use App\Models\Denuncia;
use App\Models\DenunciaLocal;
use App\Support\DenunciaCanal;
use App\Support\DenunciaStatus;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Signature('legado:importar-denuncias')]
#[Description('Importa denuncias da base de legado espelhada')]
class ImportarDenunciasLegado extends Command
{
    public function handle()
    {
        $this->info('Iniciando importacao de denuncias do banco legado...');

        try {
            DB::connection('pgsql_legado')->getPdo();
        } catch (\Exception $e) {
            $this->error('Erro de conexao com legado: '.$e->getMessage());

            return self::FAILURE;
        }

        $totalEncontrado = DB::connection('pgsql_legado')->table('denuncia')->count();
        $this->info("Total de registros a analisar: {$totalEncontrado}");

        $bar = $this->output->createProgressBar($totalEncontrado);

        DB::connection('pgsql_legado')->table('denuncia')
            ->orderBy('den_cd')
            ->chunk(1000, function ($legadas) use ($bar): void {
                foreach ($legadas as $legada) {
                    $this->importarLinha($legada);
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info('Importacao finalizada!');

        return self::SUCCESS;
    }

    private function importarLinha(object $legada): void
    {
        $dataRecebimento = $legada->den_dt_rec ? Carbon::parse($legada->den_dt_rec) : now();

        $mes = str_pad((string) $dataRecebimento->month, 2, '0', STR_PAD_LEFT);
        $ano = $dataRecebimento->year;
        $sequencia = $legada->den_numero;
        $protocolo = "{$sequencia}.{$mes}.{$ano}";

        $denuncia = Denuncia::firstOrNew([
            'origem_legado_tabela' => 'denuncia',
            'origem_legado_id' => $legada->den_cd,
        ]);

        $denuncia->importado_em = now();

        if (! $denuncia->exists) {
            $baseProtocolo = Denuncia::where('protocolo', $protocolo)->exists()
                ? $protocolo.'-'.Str::random(4)
                : $protocolo;

            $denuncia->protocolo = $baseProtocolo;
            $denuncia->token_acompanhamento_hash = hash('sha256', Str::random(32));
            $denuncia->canal = DenunciaCanal::IMPORTACAO;
            $denuncia->status = DenunciaStatus::ENCERRADA;
        }

        $denuncia->prioridade = 'normal';
        $denuncia->urgente = false;
        $denuncia->resumo = Str::limit(strip_tags((string) $legada->den_texto), 250) ?: 'Importado do legado';
        $denuncia->relato = (string) $legada->den_texto;
        $denuncia->recebida_em = $dataRecebimento;
        $denuncia->enviada_em = $dataRecebimento;
        $denuncia->save();

        $local = DenunciaLocal::firstOrNew([
            'denuncia_id' => $denuncia->id,
        ]);

        $local->pais_codigo = 'BR';
        $local->uf = $legada->den_logr_uf;
        $local->municipio = $legada->den_logr_mun;
        $local->bairro = $legada->den_logr_bairro;
        $local->subbairro = $legada->den_logr_subbairro;
        $local->logradouro_tipo = $legada->den_logr_tp;
        $local->logradouro_nome = $legada->den_logr_ds;
        $local->numero = $legada->den_logr_num;
        $local->complemento = $legada->den_logr_cmpl;
        $local->cep = $legada->den_logr_cep;
        $local->referencia = $legada->den_loc_ref;
        $local->save();
    }
}
