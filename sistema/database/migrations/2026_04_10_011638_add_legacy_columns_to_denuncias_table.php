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
        Schema::table('denuncias', function (Blueprint $table) {
            $table->bigInteger('origem_legado_id')->nullable()->index();
            $table->string('origem_legado_tabela')->nullable();
            $table->dateTime('importado_em')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('denuncias', function (Blueprint $table) {
            $table->dropColumn(['origem_legado_id', 'origem_legado_tabela', 'importado_em']);
        });
    }
};
