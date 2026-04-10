<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Denuncia;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PublicDenunciaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'relato' => 'required|string|min:10',
            'resumo' => 'nullable|string|max:255',
            'canal' => 'nullable|string',
            'local.uf' => 'nullable|string',
            'local.municipio' => 'nullable|string',
            'local.endereco_manual' => 'nullable|string',
        ]);

        $protocolo = 'DD' . date('Ymd') . strtoupper(Str::random(6));
        $token = hash('sha256', Str::random(40));

        $denuncia = Denuncia::create([
            'protocolo' => $protocolo,
            'token_acompanhamento_hash' => $token, // Ideally a hash
            'canal' => 'portal_web',
            'relato' => $validated['relato'],
            'resumo' => $validated['resumo'] ?? null,
            'ip_hash' => hash('sha256', $request->ip()),
            'user_agent_hash' => hash('sha256', $request->userAgent()),
            'recebida_em' => now(),
            'status' => 'recebida'
        ]);

        if (!empty($validated['local'])) {
            $denuncia->local()->create([
                'uf' => $validated['local']['uf'] ?? null,
                'municipio' => $validated['local']['municipio'] ?? null,
                'endereco_manual' => $validated['local']['endereco_manual'] ?? null,
            ]);
        }

        return back()->with([
            'success' => true,
            'protocolo' => $protocolo,
            'mensagem' => 'Denúncia registrada com sucesso!'
        ]);
    }
}
