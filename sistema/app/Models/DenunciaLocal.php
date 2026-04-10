<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DenunciaLocal extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $table = 'denuncia_locais';
    protected $guarded = ['id'];
}
