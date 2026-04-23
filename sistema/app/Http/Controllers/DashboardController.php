<?php

namespace App\Http\Controllers;

use App\Models\Denuncia;
use App\Support\DenunciaStatus;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $denuncias = Denuncia::query()
            ->with('local')
            ->orderByDesc('recebida_em')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Dashboard', [
            'denuncias' => $denuncias,
            'metricas' => [
                'total' => Denuncia::count(),
                'urgentes' => Denuncia::where('urgente', true)->count(),
                'novas' => Denuncia::where('status', DenunciaStatus::RECEBIDA)->count(),
            ],
        ]);
    }
}
