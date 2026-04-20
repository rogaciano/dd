<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\GrupoAssunto;
use App\Models\Assunto;
use App\Models\Etiqueta;

class DenunciaController extends Controller
{
    public function create()
    {
        // Load data for dropdowns
        $gruposAssunto = GrupoAssunto::with('assuntos')->where('ativo', true)->get();
        $etiquetas = Etiqueta::where('ativo', true)->get();

        return Inertia::render('Denuncias/Create', [
            'gruposAssunto' => $gruposAssunto,
            'etiquetas' => $etiquetas,
            'defaultUF' => env('DEFAULT_UF', 'RJ'),
            'defaultCity' => env('DEFAULT_CITY', 'Rio de Janeiro')
        ]);
    }

    public function store(Request $request)
    {
        // Implementation for saving the entire form (Denuncias, Locais, Envolvidos, Veiculos) will go here
        $data = $request->validate([
            'resumo' => 'nullable|string',
            'relato' => 'required|string',
            // fields from local, envolvidos, etc
        ]);
        
        // TODO: Map to all tables and associate 'codinome' and history logs
    }
}
