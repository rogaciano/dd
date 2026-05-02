<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orgao extends Model
{
    protected $table = 'orgaos';

    protected $guarded = ['id'];

    public function encaminhamentos()
    {
        return $this->hasMany(Encaminhamento::class);
    }
}
