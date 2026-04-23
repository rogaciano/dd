<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Models\DenunciaEnvolvido;
use App\Models\DenunciaLocal;
use App\Models\DenunciaVeiculo;
use App\Models\Etiqueta;
use App\Models\GrupoAssunto;
use App\Models\LogAuditoria;
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
            'veiculos' => 'array',
            'etiquetas' => 'array',
            'canal' => ['nullable', 'string', Rule::in(DenunciaCanal::values())],
        ]);

        DB::transaction(function () use ($data, $request): void {
            $user = $request->user();

            $denuncia = Denuncia::create([
                'relato' => $data['relato'],
                'resumo' => $data['resumo'] ?? null,
                'urgente' => false,
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
                DenunciaVeiculo::create([
                    'denuncia_id' => $denuncia->id,
                    'placa' => $veiculo['placa'] ?? null,
                    'chassis' => $veiculo['chassis'] ?? null,
                    'cor' => $veiculo['cor'] ?? null,
                    'proprietario' => $veiculo['proprietario'] ?? null,
                    'detalhes' => $veiculo['detalhes'] ?? null,
                ]);
            }

            if (! empty($data['etiquetas'])) {
                $denuncia->etiquetas()->sync($data['etiquetas']);
            }

            $codinome = $user ? ($user->codinome ?? 'Anonimo logado') : 'Sistema';

            LogAuditoria::create([
                'usuario_id' => $user?->id,
                'evento' => 'INCLUSAO_DENUNCIA',
                'entidade_tipo' => Denuncia::class,
                'entidade_id' => $denuncia->id,
                'descricao' => "Denuncia criada por {$codinome}. ".($data['comunicacao_interna'] ? "Nota Interna: {$data['comunicacao_interna']}" : ''),
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Denuncia cadastrada com sucesso!');
    }
}
