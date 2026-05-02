<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadoQuantificacao extends Model
{
    protected $table = 'resultado_quantificacoes';

    protected $guarded = ['id'];

    public function resultado()
    {
        return $this->belongsTo(Resultado::class);
    }

    public function classeItemResultado()
    {
        return $this->belongsTo(ClasseItemResultado::class, 'classe_item_resultado_id');
    }

    public function tipoItemResultado()
    {
        return $this->belongsTo(TipoItemResultado::class, 'tipo_item_resultado_id');
    }

    public function itemResultado()
    {
        return $this->belongsTo(ItemResultado::class, 'item_resultado_id');
    }

    public function unidadeMedida()
    {
        return $this->belongsTo(UnidadeMedida::class, 'unidade_medida_id');
    }
}
