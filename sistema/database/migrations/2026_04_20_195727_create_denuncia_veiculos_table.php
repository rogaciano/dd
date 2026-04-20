<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('denuncia_veiculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');
            $table->foreignId('veiculo_marca_id')->nullable()->constrained('veiculo_marcas')->onDelete('set null');
            $table->foreignId('veiculo_modelo_id')->nullable()->constrained('veiculo_modelos')->onDelete('set null');
            $table->string('cor')->nullable();
            $table->integer('ano_modelo')->nullable();
            $table->integer('ano_fabricacao')->nullable();
            $table->string('placa', 10)->nullable();
            $table->string('chassis')->nullable();
            $table->string('municipio')->nullable();
            $table->string('uf', 2)->nullable();
            $table->string('proprietario')->nullable();
            $table->text('detalhes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denuncia_veiculos');
    }
};
