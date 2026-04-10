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
        Schema::create('encaminhamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');
            $table->foreignId('orgao_id')->constrained('orgaos')->onDelete('cascade');
            $table->string('tipo')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('enviado_em')->nullable();
            $table->timestamp('prazo_em')->nullable();
            $table->text('observacoes')->nullable();
            $table->foreignId('criado_por_usuario_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encaminhamentos');
    }
};
