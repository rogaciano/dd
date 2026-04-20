<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DenunciaVeiculo extends Model
{
    protected $table = 'denuncia_veiculos';
    protected $guarded = ['id'];

    public function denuncia()
    {
        return $this->belongsTo(Denuncia::class);
    }

    public function marca()
    {
        return $this->belongsTo(VeiculoMarca::class, 'veiculo_marca_id');
    }

    public function modelo()
    {
        return $this->belongsTo(VeiculoModelo::class, 'veiculo_modelo_id');
    }
}
