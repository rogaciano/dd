<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Denuncia;
use App\Models\Etiqueta;
use App\Models\GrupoAssunto;
use App\Models\User;
use App\Support\DenunciaCanal;
use App\Support\DenunciaStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DenunciaControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_store_records_core_denuncia_relationships(): void
    {
        $user = User::factory()->create(['codinome' => 'Operador 01']);
        $grupo = GrupoAssunto::create([
            'nome' => 'Crimes Patrimoniais',
            'slug' => 'crimes-patrimoniais',
            'ativo' => true,
            'ordem_exibicao' => 1,
        ]);
        $assunto = Assunto::create([
            'grupo_assunto_id' => $grupo->id,
            'nome' => 'Roubo de Veiculo',
            'slug' => 'roubo-de-veiculo',
            'ativo' => true,
            'ordem_exibicao' => 1,
        ]);
        $etiqueta = Etiqueta::create([
            'nome' => 'Alta relevancia',
            'slug' => 'alta-relevancia',
            'ativo' => true,
        ]);

        $response = $this->actingAs($user)->post(route('denuncias.store'), [
            'relato' => 'Relato interno com informacoes suficientes para registrar a denuncia.',
            'classificacao' => 'NORMAL',
            'assunto_id' => $assunto->id,
            'difusaoImediata' => true,
            'bloqueada' => true,
            'uf' => 'RJ',
            'municipio' => 'Rio de Janeiro',
            'canal' => DenunciaCanal::INTERNO,
            'envolvidos' => [
                [
                    'papel_no_caso' => 'Suspeito',
                    'nome' => 'Pessoa Teste',
                    'apelido' => 'Vulgo Teste',
                ],
            ],
            'veiculos' => [
                [
                    'placa' => 'abc1234',
                    'marca' => 'FIAT',
                    'modelo' => 'UNO',
                    'cor' => 'Branco',
                    'proprietario' => 'Nao informado',
                ],
            ],
            'etiquetas' => [$etiqueta->id],
        ]);

        $response->assertRedirect(route('dashboard'));

        $denuncia = Denuncia::query()->firstOrFail();

        $this->assertSame(DenunciaCanal::INTERNO, $denuncia->canal);
        $this->assertSame(DenunciaStatus::RECEBIDA, $denuncia->status);
        $this->assertTrue($denuncia->urgente);
        $this->assertTrue($denuncia->bloqueada);
        $this->assertSame('alta', $denuncia->prioridade);
        $this->assertDatabaseHas('denuncia_assuntos', [
            'denuncia_id' => $denuncia->id,
            'assunto_id' => $assunto->id,
            'principal' => true,
            'criado_por_usuario_id' => $user->id,
        ]);
        $this->assertDatabaseHas('denuncia_etiqueta', [
            'denuncia_id' => $denuncia->id,
            'etiqueta_id' => $etiqueta->id,
            'criado_por_usuario_id' => $user->id,
        ]);
        $this->assertDatabaseHas('denuncia_envolvidos', [
            'denuncia_id' => $denuncia->id,
            'nome' => 'Pessoa Teste',
        ]);
        $this->assertDatabaseHas('veiculo_marcas', ['nome' => 'FIAT']);
        $this->assertDatabaseHas('veiculo_modelos', ['nome' => 'UNO']);
        $this->assertDatabaseHas('denuncia_veiculos', [
            'denuncia_id' => $denuncia->id,
            'placa' => 'ABC1234',
            'cor' => 'Branco',
        ]);
    }
}
