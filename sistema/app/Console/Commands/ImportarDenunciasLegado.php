<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('legado:importar-denuncias')]
#[Description('Importa denúncias da base de legado espelhada')]
class ImportarDenunciasLegado extends Command
{
    public function handle()
    {
        $this->info("Iniciando importacao de denuncias do banco legado...");

        try {
            \Illuminate\Support\Facades\DB::connection('pgsql_legado')->getPdo();
        } catch (\Exception $e) {
            $this->error("Erro de conexao com legado: " . $e->getMessage());
            return;
        }

        $totalEncontrado = \Illuminate\Support\Facades\DB::connection('pgsql_legado')->table('denuncia')->count();
        $this->info("Total de registros a analisar: {$totalEncontrado}");

        $bar = $this->output->createProgressBar($totalEncontrado);

        // Fetch using chunks to avoid memory issues
        \Illuminate\Support\Facades\DB::connection('pgsql_legado')->table('denuncia')
            ->orderBy('den_cd')
            ->chunk(1000, function ($legadas) use ($bar) {
                foreach ($legadas as $legada) {
                    $this->importarLinha($legada);
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info("Importacao Finalizada!");
    }

    private function importarLinha($legada)
    {
        $dataRecebimento = $legada->den_dt_rec ? \Carbon\Carbon::parse($legada->den_dt_rec) : now();

        $mes = str_pad($dataRecebimento->month, 2, '0', STR_PAD_LEFT);
        $ano = $dataRecebimento->year;
        $sequencia = $legada->den_numero;
        $protocolo = "{$sequencia}.{$mes}.{$ano}";

        $denuncia = \App\Models\Denuncia::where('origem_legado_id', $legada->den_cd)->first();

        if (!$denuncia) {
            $denuncia = new \App\Models\Denuncia();
            $denuncia->origem_legado_id = $legada->den_cd;
            $denuncia->origem_legado_tabela = 'denuncia';
        }

        $denuncia->importado_em = now();
        
        if (!$denuncia->exists) {
             // Caso na migracao existam repetidos gerando colisoes, colocamos um random pra previnir erro fatal e o ID do protocolo garante que a chave unica nao falhe
             $baseProtocolo = \App\Models\Denuncia::where('protocolo', $protocolo)->exists() ? $protocolo . '-' . \Illuminate\Support\Str::random(4) : $protocolo;
             $denuncia->protocolo = $baseProtocolo;
             $denuncia->token_acompanhamento_hash = hash('sha256', \Illuminate\Support\Str::random(32));
             $denuncia->canal = 'importacao';
             $denuncia->status = 'encerrada';
        }
        
        $denuncia->prioridade = 'normal';
        $denuncia->urgente = false;
        
        $resumo = \Illuminate\Support\Str::limit(strip_tags((string)$legada->den_texto), 250);
        $denuncia->resumo = $resumo ?: 'Importado do Legado';
        $denuncia->relato = (string)$legada->den_texto;
        $denuncia->recebida_em = $dataRecebimento;
        $denuncia->enviada_em = $dataRecebimento;

        $denuncia->save();

        $local = \App\Models\DenunciaLocal::where('denuncia_id', $denuncia->id)->first();
        if (!$local) {
            $local = new \App\Models\DenunciaLocal();
            $local->denuncia_id = $denuncia->id;
        }

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
