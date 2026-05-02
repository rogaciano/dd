<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes_item_resultado', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('tipos_item_resultado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classe_item_resultado_id')->nullable()->constrained('classes_item_resultado')->nullOnDelete();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('itens_resultado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_item_resultado_id')->nullable()->constrained('tipos_item_resultado')->nullOnDelete();
            $table->foreignId('unidade_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::table('resultado_quantificacoes', function (Blueprint $table) {
            $table->foreignId('classe_item_resultado_id')->nullable()->after('resultado_id')->constrained('classes_item_resultado')->nullOnDelete();
            $table->foreignId('tipo_item_resultado_id')->nullable()->after('classe_item_resultado_id')->constrained('tipos_item_resultado')->nullOnDelete();
            $table->foreignId('item_resultado_id')->nullable()->after('tipo_item_resultado_id')->constrained('itens_resultado')->nullOnDelete();
            $table->foreignId('unidade_medida_id')->nullable()->after('quantidade')->constrained('unidades_medida')->nullOnDelete();
            $table->unsignedBigInteger('origem_legado_id')->nullable()->after('observacoes');
            $table->string('origem_legado_tabela')->nullable()->after('origem_legado_id');
            $table->unique(['origem_legado_tabela', 'origem_legado_id'], 'resultado_quantificacoes_origem_legado_unique');
        });
    }

    public function down(): void
    {
        Schema::table('resultado_quantificacoes', function (Blueprint $table) {
            $table->dropUnique('resultado_quantificacoes_origem_legado_unique');
            $table->dropColumn(['origem_legado_tabela', 'origem_legado_id']);
            $table->dropConstrainedForeignId('unidade_medida_id');
            $table->dropConstrainedForeignId('item_resultado_id');
            $table->dropConstrainedForeignId('tipo_item_resultado_id');
            $table->dropConstrainedForeignId('classe_item_resultado_id');
        });

        Schema::dropIfExists('itens_resultado');
        Schema::dropIfExists('unidades_medida');
        Schema::dropIfExists('tipos_item_resultado');
        Schema::dropIfExists('classes_item_resultado');
    }
};
