<?php

namespace Tests\Feature;

use App\Models\Denuncia;
use App\Support\DenunciaCanal;
use App\Support\DenunciaStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDenunciaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_store_uses_normalized_status_and_channel(): void
    {
        $response = $this->post('/denunciar', [
            'relato' => 'Relato de teste suficientemente longo para passar na validacao.',
            'resumo' => 'Resumo de teste',
            'local' => [
                'uf' => 'RJ',
                'municipio' => 'Rio de Janeiro',
                'endereco_manual' => 'Centro',
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', true);

        $denuncia = Denuncia::query()->firstOrFail();

        $this->assertSame(DenunciaStatus::RECEBIDA, $denuncia->status);
        $this->assertSame(DenunciaCanal::WEB, $denuncia->canal);
        $this->assertMatchesRegularExpression('/^\d{3}\.\d{2}\.\d{4}$/', $denuncia->protocolo);
        $this->assertNotNull($denuncia->token_acompanhamento_hash);
        $this->assertDatabaseHas('denuncia_locais', [
            'denuncia_id' => $denuncia->id,
            'municipio' => 'Rio de Janeiro',
            'uf' => 'RJ',
        ]);
    }
}
