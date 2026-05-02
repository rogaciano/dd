<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denuncia_vinculos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denuncia_origem_id')->constrained('denuncias')->cascadeOnDelete();
            $table->foreignId('denuncia_relacionada_id')->constrained('denuncias')->cascadeOnDelete();
            $table->string('tipo');
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('origem_legado_id')->nullable();
            $table->string('origem_legado_tabela')->nullable();
            $table->timestamps();

            $table->index(['denuncia_origem_id', 'tipo']);
            $table->index(['denuncia_relacionada_id', 'tipo']);
            $table->unique(['origem_legado_tabela', 'origem_legado_id'], 'denuncia_vinculos_origem_legado_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denuncia_vinculos');
    }
};
