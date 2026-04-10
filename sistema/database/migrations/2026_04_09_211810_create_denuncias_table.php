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
        Schema::create('denuncias', function (Blueprint $table) {
            $table->id();
            $table->string('protocolo')->unique();
            $table->string('token_acompanhamento_hash')->nullable();
            $table->string('canal')->nullable();
            $table->string('status')->default('recebida');
            $table->string('prioridade')->default('normal');
            $table->boolean('urgente')->default(false);
            $table->text('resumo')->nullable();
            $table->text('relato');
            $table->timestamp('recebida_em')->nullable();
            $table->timestamp('enviada_em')->nullable();
            $table->foreignId('criada_por_usuario_id')->nullable()->constrained('users');
            $table->foreignId('responsavel_usuario_id')->nullable()->constrained('users');
            $table->string('ip_hash')->nullable();
            $table->string('user_agent_hash')->nullable();
            $table->timestamp('triada_em')->nullable();
            $table->timestamp('encerrada_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};
