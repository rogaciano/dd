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
        Schema::create('denuncia_envolvidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');
            $table->string('papel_no_caso')->nullable();
            $table->string('nome')->nullable();
            $table->string('apelido')->nullable();
            $table->string('sexo')->nullable();
            $table->string('idade_estimada')->nullable();
            $table->string('cor_pele')->nullable();
            $table->string('estatura')->nullable();
            $table->string('olhos')->nullable();
            $table->string('cabelo')->nullable();
            $table->string('porte_fisico')->nullable();
            $table->text('sinais_particulares')->nullable();
            $table->text('observacoes')->nullable();
            $table->text('descricao_endereco')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denuncia_envolvidos');
    }
};
