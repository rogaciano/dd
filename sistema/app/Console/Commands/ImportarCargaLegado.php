<?php

namespace App\Console\Commands;

use App\Models\Assunto;
use App\Models\ClasseItemResultado;
use App\Models\CorOlhos;
use App\Models\CorPele;
use App\Models\Denuncia;
use App\Models\DenunciaEnvolvido;
use App\Models\DenunciaLocal;
use App\Models\DenunciaMovimentacao;
use App\Models\DenunciaVeiculo;
use App\Models\DenunciaVinculo;
use App\Models\FaixaEstatura;
use App\Models\GrupoAssunto;
use App\Models\ItemResultado;
use App\Models\Orgao;
use App\Models\PorteFisico;
use App\Models\Resultado;
use App\Models\ResultadoQuantificacao;
use App\Models\TipoCabelo;
use App\Models\TipoEncaminhamento;
use App\Models\TipoItemResultado;
use App\Models\TipoResultado;
use App\Models\UnidadeMedida;
use App\Models\VeiculoMarca;
use App\Models\VeiculoModelo;
use App\Support\DenunciaCanal;
use App\Support\DenunciaStatus;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

#[Signature('legado:importar-carga {--truncate : Limpa dados importados antes da carga} {--seed : Executa os seeders basicos antes da carga} {--limit= : Limita a quantidade de denuncias importadas}')]
#[Description('Importa uma carga ampla do legado espelhado para o banco novo, para testes de volume.')]
class ImportarCargaLegado extends Command
{
    private array $assuntoMap = [];

    private array $orgaoExternoMap = [];

    private array $orgaoInternoMap = [];

    private array $resultadoTipoMap = [];

    private array $resultadoMap = [];

    private array $marcaMap = [];

    private array $modeloMap = [];

    private array $tipoEncaminhamentoMap = [];

    private array $tipoEncaminhamentoTextoMap = [];

    private array $corPeleMap = [];

    private array $corPeleTextoMap = [];

    private array $faixaEstaturaMap = [];

    private array $faixaEstaturaTextoMap = [];

    private array $corOlhosMap = [];

    private array $corOlhosTextoMap = [];

    private array $tipoCabeloMap = [];

    private array $tipoCabeloTextoMap = [];

    private array $porteFisicoMap = [];

    private array $porteFisicoTextoMap = [];

    private array $classeItemResultadoMap = [];

    private array $classeItemResultadoTextoMap = [];

    private array $tipoItemResultadoMap = [];

    private array $tipoItemResultadoTextoMap = [];

    private array $tipoItemResultadoClasseMap = [];

    private array $itemResultadoMap = [];

    private array $itemResultadoTextoMap = [];

    private array $itemResultadoTipoMap = [];

    private array $itemResultadoClasseMap = [];

    private array $itemResultadoUnidadeMap = [];

    private array $itemResultadoUnidadeTextoMap = [];

    private array $unidadeMedidaMap = [];

    private array $unidadeMedidaTextoMap = [];

    private array $denunciaLegadoIds = [];

    public function handle(): int
    {
        $legado = DB::connection('pgsql_legado');
        $novo = DB::connection();

        try {
            $legado->getPdo();
            $novo->getPdo();
        } catch (\Throwable $e) {
            $this->error('Erro de conexao: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('seed')) {
            $this->call('db:seed');
        }

        try {
            if ($novo->getDriverName() === 'pgsql') {
                $novo->statement('SET session_replication_role = replica;');
            }

            if ($this->option('truncate')) {
                $this->truncateCarga();
            }

            $this->importarAssuntos($legado);
            $this->importarOrgaos($legado);
            $this->importarTiposResultado($legado);
            $this->importarTiposEncaminhamento($legado);
            $this->importarCatalogosFisicosEnvolvidos($legado);
            $this->importarCatalogosResultadoQuantificacao($legado);
            $this->importarDenuncias($legado);
            $this->importarAssuntosDenuncia($legado);
            $this->importarCorrelatas($legado);
            $this->importarEnvolvidos($legado);
            $this->importarVeiculos($legado);
            $this->importarEncaminhamentos($legado);
            $this->importarResultados($legado);
            $this->importarMovimentacoes($legado);

            $this->newLine();
            $this->info('Carga do legado finalizada.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Erro durante a carga: '.$e->getMessage());

            return self::FAILURE;
        } finally {
            if ($novo->getDriverName() === 'pgsql') {
                $novo->statement('SET session_replication_role = DEFAULT;');
            }
        }
    }

    private function truncateCarga(): void
    {
        $this->warn('Limpando dados importados para recarga...');

        DB::table('denuncia_vinculos')->truncate();
        DB::table('resultado_quantificacoes')->truncate();
        DB::table('resultados')->truncate();
        DB::table('encaminhamentos')->truncate();
        DB::table('denuncia_movimentacoes')->truncate();
        DB::table('denuncia_veiculos')->truncate();
        DB::table('denuncia_envolvidos')->truncate();
        DB::table('denuncia_assuntos')->truncate();
        DB::table('denuncia_locais')->truncate();
        DB::table('denuncias')->where('origem_legado_tabela', 'denuncia')->delete();
        DB::table('veiculo_modelos')->truncate();
        DB::table('veiculo_marcas')->truncate();
        DB::table('orgaos')->truncate();
        DB::table('tipos_encaminhamento')->whereNotNull('origem_legado_id')->delete();
        DB::table('itens_resultado')->whereNotNull('origem_legado_id')->delete();
        DB::table('unidades_medida')->whereNotNull('origem_legado_id')->delete();
        DB::table('tipos_item_resultado')->whereNotNull('origem_legado_id')->delete();
        DB::table('classes_item_resultado')->whereNotNull('origem_legado_id')->delete();
        DB::table('portes_fisicos')->whereNotNull('origem_legado_id')->delete();
        DB::table('tipos_cabelo')->whereNotNull('origem_legado_id')->delete();
        DB::table('cores_olhos')->whereNotNull('origem_legado_id')->delete();
        DB::table('faixas_estatura')->whereNotNull('origem_legado_id')->delete();
        DB::table('cores_pele')->whereNotNull('origem_legado_id')->delete();
    }

    private function importarAssuntos($legado): void
    {
        $this->info('Importando classes e assuntos...');

        $classeMap = [];

        foreach ($legado->table('assunto_classe')->orderBy('cla_cd')->get() as $classe) {
            $nome = $this->texto($classe->cla_ds) ?: 'Classe '.$classe->cla_cd;
            $grupo = GrupoAssunto::updateOrCreate(
                ['slug' => 'legado-classe-'.$classe->cla_cd],
                [
                    'nome' => $nome,
                    'ativo' => $this->ativo($classe->cla_st),
                    'ordem_exibicao' => (int) $classe->cla_cd,
                ],
            );

            $classeMap[$classe->cla_cd] = $grupo->id;
        }

        foreach ($legado->table('assunto_tipo')->orderBy('tpa_cd')->get() as $tipo) {
            $grupoId = $classeMap[$tipo->tpa_cla_cd] ?? GrupoAssunto::query()->firstOrCreate(
                ['slug' => 'legado-sem-classe'],
                ['nome' => 'Legado sem classe', 'ordem_exibicao' => 999],
            )->id;

            $nome = $this->texto($tipo->tpa_ds) ?: 'Assunto '.$tipo->tpa_cd;
            $assunto = Assunto::updateOrCreate(
                ['slug' => 'legado-assunto-'.$tipo->tpa_cd],
                [
                    'grupo_assunto_id' => $grupoId,
                    'nome' => $nome,
                    'ativo' => $this->ativo($tipo->tpa_st),
                    'ordem_exibicao' => (int) $tipo->tpa_cd,
                ],
            );

            $this->assuntoMap[$tipo->tpa_cd] = $assunto->id;
        }
    }

    private function importarOrgaos($legado): void
    {
        $this->info('Importando orgaos...');

        $tiposExternos = $this->mapaSimples($legado->table('orgaos_externos_tipos'), 'oet_cd', 'oet_ds');

        foreach ($legado->table('orgaos_internos')->orderBy('int_cd')->get() as $interno) {
            $orgao = Orgao::updateOrCreate(
                ['nome' => $this->texto($interno->int_ds) ?: 'Orgao interno '.$interno->int_cd, 'tipo' => 'interno'],
                [
                    'categoria' => 'interno',
                    'email_destino' => $this->texto($interno->int_dest),
                    'ativo' => $this->ativo($interno->int_st),
                ],
            );

            $this->orgaoInternoMap[$interno->int_cd] = $orgao->id;
        }

        foreach ($legado->table('orgaos_externos')->orderBy('ext_cd')->get() as $externo) {
            $orgao = Orgao::updateOrCreate(
                ['nome' => $this->texto($externo->ext_ds) ?: 'Orgao externo '.$externo->ext_cd, 'tipo' => 'externo'],
                [
                    'categoria' => $tiposExternos[$externo->ext_oet_cd] ?? null,
                    'email_destino' => $this->texto($externo->ext_dest),
                    'contato_destino' => $this->texto($externo->ext_telefones),
                    'endereco' => $this->texto($externo->ext_endereco),
                    'municipio' => $this->texto($externo->ext_municipio),
                    'uf' => $this->uf($externo->ext_uf),
                    'cep' => $this->cep($externo->ext_cep),
                    'ativo' => $this->ativo($externo->ext_st),
                ],
            );

            $this->orgaoExternoMap[$externo->ext_cd] = $orgao->id;
        }
    }

    private function importarTiposResultado($legado): void
    {
        $this->info('Importando tipos de resultado...');

        foreach ($legado->table('resultado_tipo')->orderBy('rtp_cd')->get() as $tipo) {
            $nome = $this->texto($tipo->rtp_ds) ?: 'Resultado '.$tipo->rtp_cd;
            $resultadoTipo = TipoResultado::updateOrCreate(
                ['slug' => 'legado-resultado-'.$tipo->rtp_cd],
                [
                    'nome' => $nome,
                    'ativo' => $this->ativo($tipo->rtp_st),
                    'ordem_exibicao' => (int) $tipo->rtp_cd,
                ],
            );

            $this->resultadoTipoMap[$tipo->rtp_cd] = $resultadoTipo->id;
        }
    }

    private function importarTiposEncaminhamento($legado): void
    {
        $this->info('Importando tipos de encaminhamento...');

        [$this->tipoEncaminhamentoMap, $this->tipoEncaminhamentoTextoMap] = $this->importarCatalogoLegado(
            $legado,
            'difusao_tipo',
            'dit_cd',
            'dit_ds',
            TipoEncaminhamento::class,
            'legado-tipo-encaminhamento-',
        );
    }

    private function importarCatalogosFisicosEnvolvidos($legado): void
    {
        $this->info('Importando catalogos fisicos de envolvidos...');

        [$this->corPeleMap, $this->corPeleTextoMap] = $this->importarCatalogoLegado(
            $legado,
            'aux_pele',
            'pel_cd',
            'pel_ds',
            CorPele::class,
            'legado-cor-pele-',
        );

        [$this->faixaEstaturaMap, $this->faixaEstaturaTextoMap] = $this->importarCatalogoLegado(
            $legado,
            'aux_estatura',
            'est_cd',
            'est_ds',
            FaixaEstatura::class,
            'legado-faixa-estatura-',
        );

        [$this->corOlhosMap, $this->corOlhosTextoMap] = $this->importarCatalogoLegado(
            $legado,
            'aux_olhos',
            'olh_cd',
            'olh_ds',
            CorOlhos::class,
            'legado-cor-olhos-',
        );

        [$this->tipoCabeloMap, $this->tipoCabeloTextoMap] = $this->importarCatalogoLegado(
            $legado,
            'aux_cabelo',
            'cab_cd',
            'cab_ds',
            TipoCabelo::class,
            'legado-tipo-cabelo-',
        );

        [$this->porteFisicoMap, $this->porteFisicoTextoMap] = $this->importarCatalogoLegado(
            $legado,
            'aux_porte',
            'prt_cd',
            'prt_ds',
            PorteFisico::class,
            'legado-porte-fisico-',
        );
    }

    private function importarCatalogosResultadoQuantificacao($legado): void
    {
        $this->info('Importando catalogos de quantificacao de resultado...');

        [$this->classeItemResultadoMap, $this->classeItemResultadoTextoMap] = $this->importarCatalogoLegado(
            $legado,
            'item_classe',
            'cli_cd',
            'cli_ds',
            ClasseItemResultado::class,
            'legado-classe-item-',
        );

        [$this->unidadeMedidaMap, $this->unidadeMedidaTextoMap] = $this->importarCatalogoLegado(
            $legado,
            'unidades_metricas',
            'umt_cd',
            'umt_ds',
            UnidadeMedida::class,
            'legado-unidade-medida-',
        );

        if (Schema::connection('pgsql_legado')->hasTable('item_tipo')) {
            foreach ($legado->table('item_tipo')->orderBy('tpi_cd')->get() as $tipo) {
                $origemId = (int) $tipo->tpi_cd;
                $nome = $this->texto($tipo->tpi_ds);

                if ($origemId <= 0 || ! $nome) {
                    continue;
                }

                $classeId = $this->classeItemResultadoMap[$tipo->tpi_cli_cd] ?? null;

                $registro = TipoItemResultado::updateOrCreate(
                    ['origem_legado_id' => $origemId],
                    [
                        'classe_item_resultado_id' => $classeId,
                        'nome' => $nome,
                        'slug' => 'legado-tipo-item-'.$origemId,
                        'ativo' => $this->ativo($tipo->tpi_st),
                        'ordem_exibicao' => $origemId,
                    ],
                );

                $this->tipoItemResultadoMap[$origemId] = $registro->id;
                $this->tipoItemResultadoTextoMap[$origemId] = $registro->nome;
                $this->tipoItemResultadoClasseMap[$origemId] = $classeId;
            }
        }

        if (Schema::connection('pgsql_legado')->hasTable('item')) {
            foreach ($legado->table('item')->orderBy('itm_cd')->get() as $item) {
                $origemId = (int) $item->itm_cd;
                $nome = $this->texto($item->itm_ds);

                if ($origemId <= 0 || ! $nome) {
                    continue;
                }

                $tipoId = $this->tipoItemResultadoMap[$item->itm_tpi_cd] ?? null;
                $classeId = $this->tipoItemResultadoClasseMap[$item->itm_tpi_cd] ?? null;
                $unidadeId = $this->unidadeMedidaMap[$item->itm_umt_cd] ?? null;

                $registro = ItemResultado::updateOrCreate(
                    ['origem_legado_id' => $origemId],
                    [
                        'tipo_item_resultado_id' => $tipoId,
                        'unidade_medida_id' => $unidadeId,
                        'nome' => $nome,
                        'slug' => 'legado-item-resultado-'.$origemId,
                        'ativo' => $this->ativo($item->itm_st),
                        'ordem_exibicao' => $origemId,
                    ],
                );

                $this->itemResultadoMap[$origemId] = $registro->id;
                $this->itemResultadoTextoMap[$origemId] = $registro->nome;
                $this->itemResultadoTipoMap[$origemId] = $tipoId;
                $this->itemResultadoClasseMap[$origemId] = $classeId;
                $this->itemResultadoUnidadeMap[$origemId] = $unidadeId;
                $this->itemResultadoUnidadeTextoMap[$origemId] = $this->unidadeMedidaTextoMap[$item->itm_umt_cd] ?? null;
            }
        }
    }

    private function importarDenuncias($legado): void
    {
        $query = $legado->table('denuncia')->orderBy('den_cd');
        $total = $this->totalComLimite(clone $query);

        $this->info("Importando denuncias e locais ({$total})...");
        $bar = $this->output->createProgressBar($total);

        $this->percorrer($query, function ($linhas) use ($bar): void {
            foreach ($linhas as $legada) {
                $dataRecebimento = $this->data($legada->den_dt_rec) ?? now();
                $protocolo = $this->protocolo($legada, $dataRecebimento);

                $denuncia = Denuncia::firstOrNew([
                    'origem_legado_tabela' => 'denuncia',
                    'origem_legado_id' => $legada->den_cd,
                ]);

                if (! $denuncia->exists) {
                    $denuncia->protocolo = Denuncia::where('protocolo', $protocolo)->exists()
                        ? $protocolo.'-'.Str::lower(Str::random(4))
                        : $protocolo;
                    $denuncia->token_acompanhamento_hash = hash('sha256', Str::random(32));
                    $denuncia->canal = DenunciaCanal::IMPORTACAO;
                }

                $denuncia->status = DenunciaStatus::ENCERRADA;
                $denuncia->prioridade = 'normal';
                $denuncia->urgente = (bool) $legada->den_imediata;
                $denuncia->bloqueada = false;
                $denuncia->resumo = Str::limit(strip_tags((string) $legada->den_texto), 250) ?: 'Importado do legado';
                $denuncia->relato = (string) $legada->den_texto;
                $denuncia->recebida_em = $dataRecebimento;
                $denuncia->enviada_em = $dataRecebimento;
                $denuncia->encerrada_em = $this->data($legada->den_dt_alt);
                $denuncia->importado_em = now();
                $denuncia->save();
                $this->denunciaLegadoIds[] = (int) $legada->den_cd;

                DenunciaLocal::updateOrCreate(
                    ['denuncia_id' => $denuncia->id],
                    [
                        'pais_codigo' => 'BR',
                        'uf' => $this->uf($legada->den_logr_uf),
                        'municipio' => $this->texto($legada->den_logr_mun),
                        'bairro' => $this->texto($legada->den_logr_bairro),
                        'subbairro' => $this->texto($legada->den_logr_subbairro),
                        'logradouro_tipo' => $this->texto($legada->den_logr_tp),
                        'logradouro_nome' => $this->texto($legada->den_logr_ds),
                        'numero' => $this->texto($legada->den_logr_num),
                        'complemento' => $this->texto($legada->den_logr_cmpl),
                        'cep' => $this->cep($legada->den_logr_cep),
                        'referencia' => $this->texto($legada->den_loc_ref),
                    ],
                );

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
    }

    private function importarAssuntosDenuncia($legado): void
    {
        $this->info('Importando assuntos vinculados...');

        $this->importarFilhosPorDenuncia($legado->table('assunto_denuncia')->orderBy('ass_cd'), function ($linha, Denuncia $denuncia): void {
            $assuntoId = $this->assuntoMap[$linha->ass_tpa_cd] ?? null;

            if (! $assuntoId) {
                return;
            }

            DB::table('denuncia_assuntos')->updateOrInsert(
                ['denuncia_id' => $denuncia->id, 'assunto_id' => $assuntoId],
                [
                    'principal' => (bool) $linha->ass_principal,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }, 'ass_den_cd');
    }

    private function importarCorrelatas($legado): void
    {
        if (! Schema::connection('pgsql_legado')->hasTable('correlatas')) {
            return;
        }

        $this->info('Importando vinculos entre denuncias correlatas...');

        $query = $legado->table('correlatas')->orderBy('cor_cd');

        if ($this->limitado() && $this->denunciaLegadoIds !== []) {
            $query->where(function ($subQuery): void {
                $subQuery
                    ->whereIn('cor_orig_den_cd', $this->denunciaLegadoIds)
                    ->orWhereIn('cor_ref_den_cd', $this->denunciaLegadoIds);
            });
        }

        $this->percorrer($query, function ($linhas): void {
            foreach ($linhas as $linha) {
                $origem = Denuncia::query()
                    ->where('origem_legado_tabela', 'denuncia')
                    ->where('origem_legado_id', $linha->cor_orig_den_cd)
                    ->first();

                $relacionada = Denuncia::query()
                    ->where('origem_legado_tabela', 'denuncia')
                    ->where('origem_legado_id', $linha->cor_ref_den_cd)
                    ->first();

                if (! $origem || ! $relacionada || $origem->id === $relacionada->id) {
                    continue;
                }

                DenunciaVinculo::updateOrCreate(
                    [
                        'origem_legado_tabela' => 'correlatas',
                        'origem_legado_id' => $linha->cor_cd,
                    ],
                    [
                        'denuncia_origem_id' => $origem->id,
                        'denuncia_relacionada_id' => $relacionada->id,
                        'tipo' => 'correlata',
                        'observacoes' => 'Importado da tabela correlatas do legado.',
                    ],
                );
            }
        }, ! $this->limitado());
    }

    private function importarEnvolvidos($legado): void
    {
        $this->info('Importando envolvidos...');

        $this->importarFilhosPorDenuncia($legado->table('envolvidos')->orderBy('env_cd'), function ($linha, Denuncia $denuncia): void {
            DenunciaEnvolvido::updateOrCreate(
                [
                    'denuncia_id' => $denuncia->id,
                    'nome' => $this->texto($linha->env_nome),
                    'apelido' => $this->texto($linha->env_vulgo),
                ],
                [
                    'papel_no_caso' => null,
                    'sexo' => $this->texto($linha->env_sexo),
                    'idade_estimada' => $this->numeroTexto($linha->env_idade),
                    'cor_pele' => $this->textoCatalogo($this->corPeleTextoMap, $linha->env_pele, 'pele'),
                    'cor_pele_id' => $this->codigoCatalogo($this->corPeleMap, $linha->env_pele),
                    'estatura' => $this->textoCatalogo($this->faixaEstaturaTextoMap, $linha->env_estatura, 'estatura'),
                    'faixa_estatura_id' => $this->codigoCatalogo($this->faixaEstaturaMap, $linha->env_estatura),
                    'olhos' => $this->textoCatalogo($this->corOlhosTextoMap, $linha->env_olhos, 'olhos'),
                    'cor_olhos_id' => $this->codigoCatalogo($this->corOlhosMap, $linha->env_olhos),
                    'cabelo' => $this->textoCatalogo($this->tipoCabeloTextoMap, $linha->env_cabelo, 'cabelo'),
                    'tipo_cabelo_id' => $this->codigoCatalogo($this->tipoCabeloMap, $linha->env_cabelo),
                    'porte_fisico' => $this->textoCatalogo($this->porteFisicoTextoMap, $linha->env_porte, 'porte'),
                    'porte_fisico_id' => $this->codigoCatalogo($this->porteFisicoMap, $linha->env_porte),
                    'sinais_particulares' => $this->texto($linha->env_caract),
                    'observacoes' => $this->texto($linha->env_loc_ref),
                    'descricao_endereco' => $this->endereco($linha, 'env'),
                ],
            );
        }, 'env_den_cd');
    }

    private function importarVeiculos($legado): void
    {
        if (! Schema::connection('pgsql_legado')->hasTable('veiculos')) {
            return;
        }

        $this->info('Importando veiculos...');

        foreach ($legado->table('vei_marca')->orderBy('mar_cd')->get() as $marcaLegada) {
            $nome = $this->texto($marcaLegada->mar_ds);

            if (! $nome) {
                continue;
            }

            $marca = VeiculoMarca::updateOrCreate(['nome' => $nome]);
            $this->marcaMap[$marcaLegada->mar_cd] = $marca->id;
        }

        if (Schema::connection('pgsql_legado')->hasTable('vei_modelo')) {
            foreach ($legado->table('vei_modelo')->orderBy('mod_cd')->get() as $modeloLegado) {
                $nome = $this->texto($modeloLegado->mod_ds);

                if (! $nome) {
                    continue;
                }

                $modelo = VeiculoModelo::updateOrCreate(
                    ['nome' => $nome],
                    ['veiculo_marca_id' => $this->marcaMap[$modeloLegado->mar_cd] ?? null],
                );
                $this->modeloMap[$modeloLegado->mod_cd] = $modelo->id;
            }
        }

        $this->importarFilhosPorDenuncia($legado->table('veiculos')->orderBy('vei_cd'), function ($linha, Denuncia $denuncia): void {
            DenunciaVeiculo::updateOrCreate(
                [
                    'denuncia_id' => $denuncia->id,
                    'placa' => $this->texto($linha->placa),
                    'chassis' => $this->texto($linha->chassis),
                ],
                [
                    'veiculo_marca_id' => $this->marcaMap[$linha->marca] ?? null,
                    'veiculo_modelo_id' => $this->modeloMap[$linha->modelo] ?? null,
                    'cor' => $this->texto($linha->cor),
                    'ano_modelo' => $this->inteiroPositivo($linha->ano_mod),
                    'ano_fabricacao' => $this->inteiroPositivo($linha->ano_fab),
                    'municipio' => $this->texto($linha->municipio),
                    'uf' => $this->uf($linha->uf),
                    'proprietario' => $this->texto($linha->proprietario),
                    'detalhes' => $this->texto($linha->detalhes),
                ],
            );
        });
    }

    private function importarEncaminhamentos($legado): void
    {
        $this->info('Importando encaminhamentos...');

        if (Schema::connection('pgsql_legado')->hasTable('difusao_externa')) {
            $this->importarFilhosPorDenuncia($legado->table('difusao_externa')->orderBy('dex_cd'), function ($linha, Denuncia $denuncia): void {
                $orgaoId = $this->orgaoExternoMap[$linha->dex_ext_cd] ?? null;
                $tipoEncaminhamentoId = $this->tipoEncaminhamentoMap[$linha->dex_dit_cd] ?? null;
                $tipoTexto = $this->tipoEncaminhamentoTextoMap[$linha->dex_dit_cd] ?? null;

                if (! $orgaoId) {
                    return;
                }

                DB::table('encaminhamentos')->updateOrInsert(
                    ['origem_legado_tabela' => 'difusao_externa', 'origem_legado_id' => $linha->dex_cd],
                    [
                        'denuncia_id' => $denuncia->id,
                        'orgao_id' => $orgaoId,
                        'tipo_encaminhamento_id' => $tipoEncaminhamentoId,
                        'tipo' => $tipoTexto,
                        'status' => 'enviado',
                        'enviado_em' => $this->data($linha->dex_data),
                        'observacoes' => 'Importado de difusao_externa',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );
            }, 'dex_den_cd');
        }

        if (Schema::connection('pgsql_legado')->hasTable('difusao_interna')) {
            $this->importarFilhosPorDenuncia($legado->table('difusao_interna')->orderBy('din_cd'), function ($linha, Denuncia $denuncia): void {
                $orgaoId = $this->orgaoInternoMap[$linha->din_int_cd] ?? null;
                $tipoEncaminhamentoId = $this->tipoEncaminhamentoMap[$linha->din_dit_cd] ?? null;
                $tipoTexto = $this->tipoEncaminhamentoTextoMap[$linha->din_dit_cd] ?? null;

                if (! $orgaoId) {
                    return;
                }

                DB::table('encaminhamentos')->updateOrInsert(
                    ['origem_legado_tabela' => 'difusao_interna', 'origem_legado_id' => $linha->din_cd],
                    [
                        'denuncia_id' => $denuncia->id,
                        'orgao_id' => $orgaoId,
                        'tipo_encaminhamento_id' => $tipoEncaminhamentoId,
                        'tipo' => $tipoTexto,
                        'status' => 'enviado',
                        'enviado_em' => $this->data($linha->din_data),
                        'observacoes' => 'Importado de difusao_interna',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );
            }, 'din_den_cd');
        }
    }

    private function importarResultados($legado): void
    {
        $this->info('Importando resultados...');

        $this->importarFilhosPorDenuncia($legado->table('resultado_direto')->orderBy('red_cd'), function ($linha, Denuncia $denuncia): void {
            $tipoId = $this->resultadoTipoMap[$linha->red_rtp_cd] ?? null;

            if (! $tipoId) {
                return;
            }

            $resultado = Resultado::updateOrCreate(
                ['denuncia_id' => $denuncia->id, 'tipo_resultado_id' => $tipoId, 'registrado_em' => $this->data($linha->red_cad_data)],
                [
                    'orgao_id' => $this->orgaoExternoMap[$linha->red_ext_cd] ?? null,
                    'efetivado_em' => $this->data($linha->red_oper_data) ?? $this->data($linha->red_resp_data),
                    'descricao' => $this->texto($linha->red_relato),
                ],
            );

            $this->resultadoMap[$linha->red_den_cd] = $resultado->id;
        }, 'red_den_cd');

        if (Schema::connection('pgsql_legado')->hasTable('resultado_indireto')) {
            $fallbackTipoId = $this->tipoResultadoFallback();

            $this->importarFilhosPorDenuncia($legado->table('resultado_indireto')->orderBy('rei_cd'), function ($linha, Denuncia $denuncia) use ($fallbackTipoId): void {
                $resultado = Resultado::updateOrCreate(
                    ['denuncia_id' => $denuncia->id, 'tipo_resultado_id' => $fallbackTipoId, 'registrado_em' => $this->data($linha->rei_cad_data)],
                    ['descricao' => 'Resultado indireto importado do legado.'],
                );

                $this->resultadoMap[$linha->rei_den_cd] ??= $resultado->id;
            }, 'rei_den_cd');
        }

        $this->importarQuantificacoes($legado);
    }

    private function importarQuantificacoes($legado): void
    {
        if (! Schema::connection('pgsql_legado')->hasTable('quantifica_resultado')) {
            return;
        }

        $this->importarFilhosPorDenuncia($legado->table('quantifica_resultado')->orderBy('qtf_cd'), function ($linha): void {
            $resultadoId = $this->resultadoMap[$linha->qtf_den_cd] ?? null;

            if (! $resultadoId) {
                return;
            }

            $itemId = $this->itemResultadoMap[$linha->qtf_itm_cd] ?? null;
            $itemNome = $this->itemResultadoTextoMap[$linha->qtf_itm_cd] ?? 'Item '.$linha->qtf_itm_cd;
            $tipoItemId = $this->tipoItemResultadoMap[$linha->qtf_tpi_cd] ?? ($this->itemResultadoTipoMap[$linha->qtf_itm_cd] ?? null);
            $classeItemId = $this->classeItemResultadoMap[$linha->qtf_cli_cd] ?? ($this->itemResultadoClasseMap[$linha->qtf_itm_cd] ?? null);
            $classeNome = $this->classeItemResultadoTextoMap[$linha->qtf_cli_cd] ?? null;
            $unidadeMedidaId = $this->itemResultadoUnidadeMap[$linha->qtf_itm_cd] ?? null;
            $unidadeTexto = $this->itemResultadoUnidadeTextoMap[$linha->qtf_itm_cd] ?? null;

            ResultadoQuantificacao::updateOrCreate(
                [
                    'origem_legado_tabela' => 'quantifica_resultado',
                    'origem_legado_id' => $linha->qtf_cd,
                ],
                [
                    'resultado_id' => $resultadoId,
                    'classe_item_resultado_id' => $classeItemId,
                    'tipo_item_resultado_id' => $tipoItemId,
                    'item_resultado_id' => $itemId,
                    'rotulo' => $itemNome,
                    'quantidade' => $linha->qtf_qtd,
                    'unidade_medida_id' => $unidadeMedidaId,
                    'unidade' => $unidadeTexto,
                    'observacoes' => $classeNome,
                ],
            );
        }, 'qtf_den_cd');
    }

    private function importarMovimentacoes($legado): void
    {
        if (! Schema::connection('pgsql_legado')->hasTable('denuncia_com')) {
            return;
        }

        $this->info('Importando complementos como movimentacoes...');

        $this->importarFilhosPorDenuncia($legado->table('denuncia_com')->orderBy('com_cd'), function ($linha, Denuncia $denuncia): void {
            DenunciaMovimentacao::updateOrCreate(
                ['denuncia_id' => $denuncia->id, 'tipo' => 'complemento_legado', 'titulo' => 'Complemento do legado'],
                [
                    'conteudo' => $this->texto($linha->den_com),
                    'visibilidade' => 'interna',
                ],
            );
        }, 'den_cd');
    }

    private function importarFilhosPorDenuncia(Builder $query, callable $callback, string $denunciaColumn = 'den_cd'): void
    {
        if ($this->limitado() && $this->denunciaLegadoIds === []) {
            return;
        }

        if ($this->limitado()) {
            $query->whereIn($denunciaColumn, $this->denunciaLegadoIds);
        }

        $this->percorrer($query, function ($linhas) use ($callback, $denunciaColumn): void {
            foreach ($linhas as $linha) {
                $denuncia = Denuncia::query()
                    ->where('origem_legado_tabela', 'denuncia')
                    ->where('origem_legado_id', $linha->{$denunciaColumn})
                    ->first();

                if (! $denuncia) {
                    continue;
                }

                $callback($linha, $denuncia);
            }
        }, false);
    }

    private function percorrer(Builder $query, callable $callback, bool $usarLimite = true): void
    {
        if ($usarLimite && $this->limitado()) {
            $linhas = $query->limit($this->limite())->get();

            if ($linhas->isNotEmpty()) {
                $callback($linhas);
            }

            return;
        }

        $query->chunk(1000, $callback);
    }

    private function totalComLimite(Builder $query): int
    {
        $total = $query->count();

        if ($this->limitado()) {
            return min($total, $this->limite());
        }

        return $total;
    }

    private function limitado(): bool
    {
        $limit = $this->option('limit');

        return $limit !== null && $limit !== '';
    }

    private function limite(): int
    {
        return max(0, (int) $this->option('limit'));
    }

    private function mapaSimples(Builder $query, string $idColumn, string $textColumn): array
    {
        return $query->get()
            ->mapWithKeys(fn ($linha) => [$linha->{$idColumn} => $this->texto($linha->{$textColumn})])
            ->all();
    }

    private function importarCatalogoLegado(
        $legado,
        string $table,
        string $idColumn,
        string $textColumn,
        string $modelClass,
        string $slugPrefix,
    ): array {
        if (! Schema::connection('pgsql_legado')->hasTable($table)) {
            return [[], []];
        }

        $ids = [];
        $textos = [];

        foreach ($legado->table($table)->orderBy($idColumn)->get() as $linha) {
            $origemId = (int) $linha->{$idColumn};
            $nome = $this->texto($linha->{$textColumn});

            if ($origemId <= 0 || ! $nome) {
                continue;
            }

            $registro = $modelClass::updateOrCreate(
                ['origem_legado_id' => $origemId],
                [
                    'nome' => $nome,
                    'slug' => $slugPrefix.$origemId,
                    'ativo' => true,
                    'ordem_exibicao' => $origemId,
                ],
            );

            $ids[$origemId] = $registro->id;
            $textos[$origemId] = $registro->nome;
        }

        return [$ids, $textos];
    }

    private function protocolo(object $legada, Carbon $dataRecebimento): string
    {
        $numero = (string) ($legada->den_numero ?: $legada->den_cd);

        return $numero.'.'.$dataRecebimento->format('m').'.'.$dataRecebimento->format('Y');
    }

    private function tipoResultadoFallback(): int
    {
        return TipoResultado::firstOrCreate(
            ['slug' => 'legado-resultado-indireto'],
            ['nome' => 'Resultado indireto legado', 'ordem_exibicao' => 999],
        )->id;
    }

    private function endereco(object $linha, string $prefixo): ?string
    {
        $partes = array_filter([
            $this->texto($linha->{$prefixo.'_logr_tp'} ?? null),
            $this->texto($linha->{$prefixo.'_logr_ds'} ?? null),
            $this->texto($linha->{$prefixo.'_logr_num'} ?? null),
            $this->texto($linha->{$prefixo.'_logr_cmpl'} ?? null),
            $this->texto($linha->{$prefixo.'_logr_bairro'} ?? null),
            $this->texto($linha->{$prefixo.'_logr_subbairro'} ?? null),
            $this->texto($linha->{$prefixo.'_logr_mun'} ?? null),
            $this->uf($linha->{$prefixo.'_logr_uf'} ?? null),
        ]);

        return $partes ? implode(', ', $partes) : null;
    }

    private function texto(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text !== '' ? str_replace("\0", '', $text) : null;
    }

    private function uf(mixed $value): ?string
    {
        $uf = Str::upper((string) $this->texto($value));

        return strlen($uf) === 2 ? $uf : null;
    }

    private function cep(mixed $value): ?string
    {
        $cep = preg_replace('/\D+/', '', (string) $value);

        return $cep !== '' && (int) $cep > 0 ? str_pad($cep, 8, '0', STR_PAD_LEFT) : null;
    }

    private function data(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value);
    }

    private function ativo(mixed $value): bool
    {
        return (int) $value === 0;
    }

    private function inteiroPositivo(mixed $value): ?int
    {
        $int = (int) $value;

        return $int > 0 ? $int : null;
    }

    private function numeroTexto(mixed $value): ?string
    {
        $int = (int) $value;

        return $int > 0 ? (string) $int : null;
    }

    private function codigoCatalogo(array $map, mixed $value): ?int
    {
        $int = (int) $value;

        return $int > 0 ? ($map[$int] ?? null) : null;
    }

    private function textoCatalogo(array $map, mixed $value, string $fallbackLabel): ?string
    {
        $int = (int) $value;

        if ($int <= 0) {
            return null;
        }

        return $map[$int] ?? $this->codigoTexto($fallbackLabel, $int);
    }

    private function codigoTexto(string $label, mixed $value): ?string
    {
        $int = (int) $value;

        return $int > 0 ? $label.' '.$int : null;
    }
}
