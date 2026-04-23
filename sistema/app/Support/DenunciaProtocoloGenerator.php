<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class DenunciaProtocoloGenerator
{
    public function generate(?CarbonInterface $referenceDate = null): string
    {
        $date = $referenceDate ?? now();
        $year = (int) $date->format('Y');
        $month = (int) $date->format('m');
        $timestamp = now();

        return DB::transaction(function () use ($year, $month, $timestamp): string {
            DB::table('denuncia_protocolo_sequencias')->upsert(
                [[
                    'ano' => $year,
                    'mes' => $month,
                    'ultimo_numero' => 0,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]],
                ['ano', 'mes'],
                ['updated_at'],
            );

            $registro = DB::table('denuncia_protocolo_sequencias')
                ->where('ano', $year)
                ->where('mes', $month)
                ->lockForUpdate()
                ->first();

            $proximoNumero = ((int) $registro->ultimo_numero) + 1;

            DB::table('denuncia_protocolo_sequencias')
                ->where('id', $registro->id)
                ->update([
                    'ultimo_numero' => $proximoNumero,
                    'updated_at' => now(),
                ]);

            return sprintf('%03d.%02d.%04d', $proximoNumero, $month, $year);
        }, 5);
    }
}
