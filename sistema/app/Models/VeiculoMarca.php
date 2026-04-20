<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VeiculoMarca extends Model
{
    protected $table = 'veiculo_marcas';
    protected $guarded = ['id'];

    public function modelos()
    {
        return $this->hasMany(VeiculoModelo::class);
    }
}
