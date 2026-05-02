<?php

namespace App\Models;

use App\Support\DenunciaCanal;
use App\Support\DenunciaProtocoloGenerator;
use App\Support\DenunciaStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denuncia extends Model
{
    use HasFactory;

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

    public function encaminhamentos()
    {
        return $this->hasMany(Encaminhamento::class);
    }

    public function resultados()
    {
        return $this->hasMany(Resultado::class);
    }

    public function vinculosOrigem()
    {
        return $this->hasMany(DenunciaVinculo::class, 'denuncia_origem_id');
    }

    public function vinculosRelacionados()
    {
        return $this->hasMany(DenunciaVinculo::class, 'denuncia_relacionada_id');
    }

    protected function casts(): array
    {
        return [
            'urgente' => 'boolean',
            'bloqueada' => 'boolean',
            'recebida_em' => 'datetime',
            'enviada_em' => 'datetime',
            'triada_em' => 'datetime',
            'encerrada_em' => 'datetime',
            'importado_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Denuncia $denuncia) {
            $referenceDate = $denuncia->recebida_em ?? now();

            if (empty($denuncia->protocolo)) {
                $denuncia->protocolo = app(DenunciaProtocoloGenerator::class)->generate($referenceDate);
            }

            $denuncia->status ??= DenunciaStatus::RECEBIDA;
            $denuncia->canal ??= DenunciaCanal::WEB;
            $denuncia->recebida_em ??= $referenceDate;
        });
    }
}
