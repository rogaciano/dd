<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    protected $table = 'resultados';

    protected $guarded = ['id'];

    public function denuncia()
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function tipoResultado()
    {
        return $this->belongsTo(TipoResultado::class);
    }

    public function orgao()
    {
        return $this->belongsTo(Orgao::class);
    }

    public function quantificacoes()
    {
        return $this->hasMany(ResultadoQuantificacao::class, 'resultado_id');
    }
}
