<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encaminhamento extends Model
{
    protected $table = 'encaminhamentos';

    protected $guarded = ['id'];

    public function denuncia()
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function orgao()
    {
        return $this->belongsTo(Orgao::class);
    }

    public function tipoEncaminhamento()
    {
        return $this->belongsTo(TipoEncaminhamento::class);
    }
}
