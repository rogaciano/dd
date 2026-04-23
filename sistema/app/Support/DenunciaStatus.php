<?php

namespace App\Support;

final class DenunciaStatus
{
    public const RASCUNHO = 'rascunho';

    public const RECEBIDA = 'recebida';

    public const TRIAGEM = 'triagem';

    public const CLASSIFICADA = 'classificada';

    public const ENCAMINHADA = 'encaminhada';

    public const EM_ANDAMENTO = 'em_andamento';

    public const RESOLVIDA = 'resolvida';

    public const ENCERRADA = 'encerrada';

    public const ARQUIVADA = 'arquivada';

    public static function values(): array
    {
        return [
            self::RASCUNHO,
            self::RECEBIDA,
            self::TRIAGEM,
            self::CLASSIFICADA,
            self::ENCAMINHADA,
            self::EM_ANDAMENTO,
            self::RESOLVIDA,
            self::ENCERRADA,
            self::ARQUIVADA,
        ];
    }
}
