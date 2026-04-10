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
        Schema::create('resultados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');
            $table->foreignId('tipo_resultado_id')->constrained('tipos_resultado')->onDelete('cascade');
            $table->foreignId('orgao_id')->nullable()->constrained('orgaos');
            $table->timestamp('registrado_em')->nullable();
            $table->timestamp('efetivado_em')->nullable();
            $table->text('descricao')->nullable();
            $table->foreignId('criado_por_usuario_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};
