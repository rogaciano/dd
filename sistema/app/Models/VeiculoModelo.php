<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VeiculoModelo extends Model
{
    protected $table = 'veiculo_modelos';
    protected $guarded = ['id'];

    public function marca()
    {
        return $this->belongsTo(VeiculoMarca::class);
    }
}
