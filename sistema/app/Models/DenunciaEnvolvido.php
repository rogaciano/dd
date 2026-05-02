<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DenunciaEnvolvido extends Model
{
    protected $table = 'denuncia_envolvidos';

    protected $guarded = ['id'];

    public function denuncia()
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function corPele()
    {
        return $this->belongsTo(CorPele::class, 'cor_pele_id');
    }

    public function faixaEstatura()
    {
        return $this->belongsTo(FaixaEstatura::class, 'faixa_estatura_id');
    }

    public function corOlhos()
    {
        return $this->belongsTo(CorOlhos::class, 'cor_olhos_id');
    }

    public function tipoCabelo()
    {
        return $this->belongsTo(TipoCabelo::class, 'tipo_cabelo_id');
    }

    public function porteFisico()
    {
        return $this->belongsTo(PorteFisico::class, 'porte_fisico_id');
    }
}
