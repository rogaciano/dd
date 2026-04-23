<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('denuncias', function (Blueprint $table) {
            $table->unique(['origem_legado_tabela', 'origem_legado_id'], 'denuncias_origem_legado_unique');
            $table->index(['canal', 'status'], 'denuncias_canal_status_index');
            $table->index('recebida_em', 'denuncias_recebida_em_index');
        });
    }

    public function down(): void
    {
        Schema::table('denuncias', function (Blueprint $table) {
            $table->dropUnique('denuncias_origem_legado_unique');
            $table->dropIndex('denuncias_canal_status_index');
            $table->dropIndex('denuncias_recebida_em_index');
        });
    }
};
