<?php

namespace Tests\Feature;

use App\Models\Denuncia;
use App\Support\DenunciaCanal;
use App\Support\DenunciaStatus;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ImportLegadoVeiculosTest extends TestCase
{
    use RefreshDatabase;

    private string $legacyDatabasePath;

    protected function setUp(): void
    {
        if (! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite nao esta disponivel no PHP atual. Rode os testes no container da aplicacao.');
        }

        parent::setUp();

        $this->legacyDatabasePath = database_path('testing_legado.sqlite');

        if (file_exists($this->legacyDatabasePath)) {
            unlink($this->legacyDatabasePath);
        }

        touch($this->legacyDatabasePath);

        config()->set('database.connections.pgsql_legado', [
            'driver' => 'sqlite',
            'database' => $this->legacyDatabasePath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        DB::purge('pgsql_legado');

        $this->createLegacySchema();
    }

    protected function tearDown(): void
    {
        if (isset($this->app)) {
            DB::purge('pgsql_legado');
        }

        if (isset($this->legacyDatabasePath) && file_exists($this->legacyDatabasePath)) {
            unlink($this->legacyDatabasePath);
        }

        parent::tearDown();
    }

    public function test_command_links_vehicle_using_legacy_origin_id(): void
    {
        $denuncia = Denuncia::create([
            'relato' => 'Denuncia importada do legado',
            'canal' => DenunciaCanal::IMPORTACAO,
            'status' => DenunciaStatus::ENCERRADA,
            'origem_legado_tabela' => 'denuncia',
            'origem_legado_id' => 100,
            'recebida_em' => now(),
        ]);

        DB::connection('pgsql_legado')->table('vei_marca')->insert([
            'mar_cd' => 1,
            'mar_ds' => 'Volkswagen',
        ]);

        DB::connection('pgsql_legado')->table('vei_modelo')->insert([
            'mod_cd' => 10,
            'mar_cd' => 1,
            'mod_ds' => 'Gol',
        ]);

        DB::connection('pgsql_legado')->table('veiculos')->insert([
            'den_cd' => 100,
            'marca' => 1,
            'modelo' => 10,
            'placa' => 'ABC1234',
            'cor' => 'Branco',
            'ano_mod' => 2020,
            'ano_fab' => 2019,
            'chassis' => 'CHS123',
            'municipio' => 'Rio de Janeiro',
            'uf' => 'RJ',
            'proprietario' => 'Fulano',
            'detalhes' => 'Veiculo de teste',
        ]);

        $this->artisan('legado:importar-veiculos')->assertSuccessful();

        $this->assertDatabaseHas('veiculo_marcas', [
            'nome' => 'Volkswagen',
        ]);

        $this->assertDatabaseHas('veiculo_modelos', [
            'nome' => 'Gol',
        ]);

        $this->assertDatabaseHas('denuncia_veiculos', [
            'denuncia_id' => $denuncia->id,
            'placa' => 'ABC1234',
            'municipio' => 'Rio de Janeiro',
            'uf' => 'RJ',
        ]);
    }

    private function createLegacySchema(): void
    {
        Schema::connection('pgsql_legado')->create('vei_marca', function (Blueprint $table): void {
            $table->integer('mar_cd')->primary();
            $table->string('mar_ds');
        });

        Schema::connection('pgsql_legado')->create('vei_modelo', function (Blueprint $table): void {
            $table->integer('mod_cd')->primary();
            $table->integer('mar_cd');
            $table->string('mod_ds');
        });

        Schema::connection('pgsql_legado')->create('veiculos', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('den_cd');
            $table->integer('marca');
            $table->integer('modelo');
            $table->string('placa')->nullable();
            $table->string('cor')->nullable();
            $table->integer('ano_mod')->nullable();
            $table->integer('ano_fab')->nullable();
            $table->string('chassis')->nullable();
            $table->string('municipio')->nullable();
            $table->string('uf')->nullable();
            $table->string('proprietario')->nullable();
            $table->string('detalhes')->nullable();
        });
    }
}
