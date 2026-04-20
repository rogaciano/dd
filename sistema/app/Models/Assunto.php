<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assunto extends Model
{
    protected $table = 'assuntos';
    protected $guarded = ['id'];

    public function grupo_assunto()
    {
        return $this->belongsTo(GrupoAssunto::class, 'grupo_assunto_id');
    }
}
