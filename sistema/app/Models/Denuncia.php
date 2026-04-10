<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'denuncias';
    protected $guarded = ['id'];

    public function local()
    {
        return $this->hasOne(DenunciaLocal::class);
    }

    public function assuntos()
    {
        return $this->belongsToMany(Assunto::class, 'denuncia_assuntos', 'denuncia_id', 'assunto_id');
    }
}
