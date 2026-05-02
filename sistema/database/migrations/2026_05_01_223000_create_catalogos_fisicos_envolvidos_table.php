<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cores_pele', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('faixas_estatura', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('cores_olhos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('tipos_cabelo', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('portes_fisicos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug')->unique();
            $table->boolean('ativo')->default(true);
            $table->unsignedInteger('ordem_exibicao')->default(0);
            $table->unsignedInteger('origem_legado_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portes_fisicos');
        Schema::dropIfExists('tipos_cabelo');
        Schema::dropIfExists('cores_olhos');
        Schema::dropIfExists('faixas_estatura');
        Schema::dropIfExists('cores_pele');
    }
};
