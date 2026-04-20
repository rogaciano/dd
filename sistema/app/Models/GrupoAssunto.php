<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoAssunto extends Model
{
    protected $table = 'grupos_assunto';
    protected $guarded = ['id'];

    public function assuntos()
    {
        return $this->hasMany(Assunto::class, 'grupo_assunto_id');
    }
}
