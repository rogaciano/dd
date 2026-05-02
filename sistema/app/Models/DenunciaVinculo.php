<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DenunciaVinculo extends Model
{
    protected $table = 'denuncia_vinculos';

    protected $guarded = ['id'];

    public function denunciaOrigem()
    {
        return $this->belongsTo(Denuncia::class, 'denuncia_origem_id');
    }

    public function denunciaRelacionada()
    {
        return $this->belongsTo(Denuncia::class, 'denuncia_relacionada_id');
    }
}
