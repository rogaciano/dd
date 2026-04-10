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
        Schema::create('anexos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('denuncia_id')->constrained('denuncias')->onDelete('cascade');
            $table->string('disco')->nullable();
            $table->string('caminho');
            $table->string('nome_original')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('tamanho')->nullable();
            $table->string('checksum')->nullable();
            $table->foreignId('enviado_por_usuario_id')->nullable()->constrained('users');
            $table->timestamp('enviado_em')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anexos');
    }
};
