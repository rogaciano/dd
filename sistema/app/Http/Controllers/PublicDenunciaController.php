<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Support\DenunciaCanal;
use App\Support\DenunciaStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PublicDenunciaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'relato' => 'required|string|min:10',
            'resumo' => 'nullable|string|max:255',
            'canal' => ['nullable', 'string', Rule::in([DenunciaCanal::WEB, DenunciaCanal::TELEFONE])],
            'local.uf' => 'nullable|string',
            'local.municipio' => 'nullable|string',
            'local.endereco_manual' => 'nullable|string',
        ]);

        $token = Str::upper(Str::random(10));

        $denuncia = Denuncia::create([
            'token_acompanhamento_hash' => hash('sha256', $token),
            'canal' => $validated['canal'] ?? DenunciaCanal::WEB,
            'relato' => $validated['relato'],
            'resumo' => $validated['resumo'] ?? null,
            'ip_hash' => hash('sha256', (string) $request->ip()),
            'user_agent_hash' => hash('sha256', (string) $request->userAgent()),
            'recebida_em' => now(),
            'status' => DenunciaStatus::RECEBIDA,
        ]);

        if (! empty($validated['local'])) {
            $denuncia->local()->create([
                'uf' => $validated['local']['uf'] ?? null,
                'municipio' => $validated['local']['municipio'] ?? null,
                'endereco_manual' => $validated['local']['endereco_manual'] ?? null,
            ]);
        }

        return back()->with([
            'success' => true,
            'protocolo' => $denuncia->protocolo,
            'token_acompanhamento' => $token,
            'mensagem' => 'Denuncia registrada com sucesso!',
        ]);
    }
}
