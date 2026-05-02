<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('denuncia_envolvidos', function (Blueprint $table) {
            $table->foreignId('cor_pele_id')->nullable()->after('cor_pele')->constrained('cores_pele')->nullOnDelete();
            $table->foreignId('faixa_estatura_id')->nullable()->after('estatura')->constrained('faixas_estatura')->nullOnDelete();
            $table->foreignId('cor_olhos_id')->nullable()->after('olhos')->constrained('cores_olhos')->nullOnDelete();
            $table->foreignId('tipo_cabelo_id')->nullable()->after('cabelo')->constrained('tipos_cabelo')->nullOnDelete();
            $table->foreignId('porte_fisico_id')->nullable()->after('porte_fisico')->constrained('portes_fisicos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('denuncia_envolvidos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('porte_fisico_id');
            $table->dropConstrainedForeignId('tipo_cabelo_id');
            $table->dropConstrainedForeignId('cor_olhos_id');
            $table->dropConstrainedForeignId('faixa_estatura_id');
            $table->dropConstrainedForeignId('cor_pele_id');
        });
    }
};
