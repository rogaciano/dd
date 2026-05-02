<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_encaminhamento', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::table('encaminhamentos', function (Blueprint $table) {
            $table->foreignId('tipo_encaminhamento_id')->nullable()->after('orgao_id')->constrained('tipos_encaminhamento')->nullOnDelete();
            $table->unsignedBigInteger('origem_legado_id')->nullable()->after('criado_por_usuario_id');
            $table->string('origem_legado_tabela')->nullable()->after('origem_legado_id');
            $table->unique(['origem_legado_tabela', 'origem_legado_id'], 'encaminhamentos_origem_legado_unique');
        });
    }

    public function down(): void
    {
        Schema::table('encaminhamentos', function (Blueprint $table) {
            $table->dropUnique('encaminhamentos_origem_legado_unique');
            $table->dropColumn(['origem_legado_tabela', 'origem_legado_id']);
            $table->dropConstrainedForeignId('tipo_encaminhamento_id');
        });

        Schema::dropIfExists('tipos_encaminhamento');
    }
};
