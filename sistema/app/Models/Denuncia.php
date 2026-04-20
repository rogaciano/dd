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

    public function veiculos()
    {
        return $this->hasMany(DenunciaVeiculo::class);
    }

    public function etiquetas()
    {
        return $this->belongsToMany(Etiqueta::class, 'denuncia_etiqueta', 'denuncia_id', 'etiqueta_id');
    }

    protected static function booted(): void
    {
        static::creating(function (Denuncia $denuncia) {
            if (empty($denuncia->protocolo)) {
                $month = date('m');
                $year = date('Y');
                
                $lastDoc = self::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('id', 'desc')
                    ->first();
                
                $nextNum = 1;
                if ($lastDoc) {
                    $parts = explode('.', $lastDoc->protocolo);
                    if (isset($parts[0])) {
                        $nextNum = intval($parts[0]) + 1;
                    }
                }
                
                $denuncia->protocolo = sprintf('%03d.%s.%s', $nextNum, $month, $year);
            }
        });
    }
}
