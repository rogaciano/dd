<?php

namespace Tests\Feature;

use App\Models\Denuncia;
use App\Support\DenunciaCanal;
use App\Support\DenunciaStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DenunciaProtocolGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_protocols_are_allocated_sequentially_per_month(): void
    {
        $primeira = Denuncia::create([
            'relato' => 'Primeira denuncia',
            'canal' => DenunciaCanal::WEB,
            'status' => DenunciaStatus::RECEBIDA,
            'recebida_em' => Carbon::parse('2026-04-21 10:00:00'),
        ]);

        $segunda = Denuncia::create([
            'relato' => 'Segunda denuncia',
            'canal' => DenunciaCanal::WEB,
            'status' => DenunciaStatus::RECEBIDA,
            'recebida_em' => Carbon::parse('2026-04-21 11:00:00'),
        ]);

        $terceira = Denuncia::create([
            'relato' => 'Terceira denuncia',
            'canal' => DenunciaCanal::WEB,
            'status' => DenunciaStatus::RECEBIDA,
            'recebida_em' => Carbon::parse('2026-05-01 09:00:00'),
        ]);

        $this->assertSame('001.04.2026', $primeira->protocolo);
        $this->assertSame('002.04.2026', $segunda->protocolo);
        $this->assertSame('001.05.2026', $terceira->protocolo);
    }
}
