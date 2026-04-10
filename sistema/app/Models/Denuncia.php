<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
    protected $table = 'denuncias';
    protected $guarded = ['id'];

    public function local()
    {
        return $this->hasOne(DenunciaLocal::class);
    }
}
