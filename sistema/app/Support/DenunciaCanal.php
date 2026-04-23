<?php

namespace App\Support;

final class DenunciaCanal
{
    public const WEB = 'web';

    public const TELEFONE = 'telefone';

    public const INTERNO = 'interno';

    public const IMPORTACAO = 'importacao';

    public static function values(): array
    {
        return [
            self::WEB,
            self::TELEFONE,
            self::INTERNO,
            self::IMPORTACAO,
        ];
    }
}
