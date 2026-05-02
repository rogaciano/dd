<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Models\DenunciaEnvolvido;
use App\Models\DenunciaLocal;
use App\Models\DenunciaVeiculo;
use App\Models\Etiqueta;
use App\Models\GrupoAssunto;
use App\Models\LogAuditoria;
use App\Models\VeiculoMarca;
use App\Models\VeiculoModelo;
use App\Support\DenunciaCanal;
use App\Support\DenunciaStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DenunciaController extends Controller
{
    public function create()
    {
        $gruposAssunto = GrupoAssunto::with('assuntos')->where('ativo', true)->get();
        $etiquetas = Etiqueta::where('ativo', true)->get();

        return Inertia::render('Denuncias/Create', [
            'gruposAssunto' => $gruposAssunto,
            'etiquetas' => $etiquetas,
            'defaultUF' => env('DEFAULT_UF', 'RJ'),
            'defaultCity' => env('DEFAULT_CITY', 'Rio de Janeiro'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'resumo' => 'nullable|string',
            'relato' => 'required|string',
            'classificacao' => 'required|string',
            'assunto_id' => 'required|integer|exists:assuntos,id',
            'difusaoImediata' => 'boolean',
            'bloqueada' => 'boolean',
            'cep' => 'nullable|string',
            'logradouro' => 'nullable|string',
            'numero' => 'nullable|string',
            'complemento' => 'nullable|string',
            'bairro' => 'nullable|string',
            'ponto_referencia' => 'nullable|string',
            'uf' => 'required|string',
            'municipio' => 'required|string',
            'comunicacao_interna' => 'nullable|string',
            'envolvidos' => 'array',
            'envolvidos.*.papel_no_caso' => 'nullable|string|max:100',
            'envolvidos.*.nome' => 'nullable|string|max:255',
            'envolvidos.*.apelido' => 'nullable|string|max:255',
            'envolvidos.*.sexo' => 'nullable|string|max:50',
            'envolvidos.*.idade_estimada' => 'nullable|string|max:50',
            'envolvidos.*.cor_pele' => 'nullable|string|max:100',
            'envolvidos.*.porte_fisico' => 'nullable|string|max:100',
            'envolvidos.*.sinais_particulares' => 'nullable|string',
            'veiculos' => 'array',
            'veiculos.*.placa' => 'nullable|string|max:10',
            'veiculos.*.chassis' => 'nullable|string|max:255',
            'veiculos.*.marca' => 'nullable|string|max:255',
            'veiculos.*.modelo' => 'nullable|string|max:255',
            'veiculos.*.cor' => 'nullable|string|max:255',
            'veiculos.*.proprietario' => 'nullable|string|max:255',
            'veiculos.*.detalhes' => 'nullable|string',
            'etiquetas' => 'array',
            'etiquetas.*' => 'integer|exists:etiquetas,id',
            'canal' => ['nullable', 'string', Rule::in(DenunciaCanal::values())],
        ]);

        DB::transaction(function () use ($data, $request): void {
            $user = $request->user();
            $urgente = (bool) ($data['difusaoImediata'] ?? false);

            $denuncia = Denuncia::create([
                'relato' => $data['relato'],
                'resumo' => $data['resumo'] ?? null,
                'urgente' => $urgente,
                'prioridade' => $urgente ? 'alta' : 'normal',
                'status' => DenunciaStatus::RECEBIDA,
                'bloqueada' => $data['bloqueada'] ?? false,
                'canal' => $data['canal'] ?? DenunciaCanal::INTERNO,
                'recebida_em' => now(),
                'criada_por_usuario_id' => $user?->id,
            ]);

            DenunciaLocal::create([
                'denuncia_id' => $denuncia->id,
                'cep' => $data['cep'] ?? null,
                'logradouro_nome' => $data['logradouro'] ?? null,
                'numero' => $data['numero'] ?? null,
                'complemento' => $data['complemento'] ?? null,
                'bairro' => $data['bairro'] ?? null,
                'municipio' => $data['municipio'],
                'uf' => $data['uf'],
                'referencia' => $data['ponto_referencia'] ?? null,
            ]);

            foreach ($data['envolvidos'] ?? [] as $envolvido) {
                DenunciaEnvolvido::create([
                    'denuncia_id' => $denuncia->id,
                    'papel_no_caso' => $envolvido['papel_no_caso'] ?? null,
                    'nome' => $envolvido['nome'] ?? null,
                    'apelido' => $envolvido['apelido'] ?? null,
                    'sexo' => $envolvido['sexo'] ?? null,
                    'idade_estimada' => $envolvido['idade_estimada'] ?? null,
                    'cor_pele' => $envolvido['cor_pele'] ?? null,
                    'porte_fisico' => $envolvido['porte_fisico'] ?? null,
                    'sinais_particulares' => $envolvido['sinais_particulares'] ?? null,
                ]);
            }

            foreach ($data['veiculos'] ?? [] as $veiculo) {
                $marcaId = null;
                $modeloId = null;
                $marcaNome = $this->blankToNull($veiculo['marca'] ?? null);
                $modeloNome = $this->blankToNull($veiculo['modelo'] ?? null);

                if ($marcaNome) {
                    $marcaId = VeiculoMarca::firstOrCreate(['nome' => $marcaNome])->id;
                }

                if ($modeloNome) {
                    $modeloId = VeiculoModelo::firstOrCreate([
                        'veiculo_marca_id' => $marcaId,
                        'nome' => $modeloNome,
                    ])->id;
                }

                DenunciaVeiculo::create([
                    'denuncia_id' => $denuncia->id,
                    'veiculo_marca_id' => $marcaId,
                    'veiculo_modelo_id' => $modeloId,
                    'placa' => $this->blankToNull(strtoupper((string) ($veiculo['placa'] ?? ''))),
                    'chassis' => $veiculo['chassis'] ?? null,
                    'cor' => $veiculo['cor'] ?? null,
                    'proprietario' => $veiculo['proprietario'] ?? null,
                    'detalhes' => $veiculo['detalhes'] ?? null,
                ]);
            }

            $denuncia->assuntos()->attach($data['assunto_id'], [
                'principal' => true,
                'criado_por_usuario_id' => $user?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (! empty($data['etiquetas'])) {
                $etiquetas = collect($data['etiquetas'])->mapWithKeys(fn ($etiquetaId) => [
                    $etiquetaId => [
                        'criado_por_usuario_id' => $user?->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);

                $denuncia->etiquetas()->sync($etiquetas->all());
            }

            $codinome = $user ? ($user->codinome ?? 'Anonimo logado') : 'Sistema';
            $notaInterna = $data['comunicacao_interna'] ?? null;

            LogAuditoria::create([
                'usuario_id' => $user?->id,
                'evento' => 'INCLUSAO_DENUNCIA',
                'entidade_tipo' => Denuncia::class,
                'entidade_id' => $denuncia->id,
                'descricao' => "Denuncia criada por {$codinome}. ".($notaInterna ? "Nota Interna: {$notaInterna}" : ''),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Denuncia cadastrada com sucesso!');
    }

    private function blankToNull(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
