<?php

namespace App\Enums;

enum VoteOption: string
{
    case Yes = 'Yes';
    case No = 'No';

    /**
     * Label legível para o associado (usado nas mensagens de UI do Anexo 1).
     * O value do enum continua em inglês porque é o contrato da API.
     */
    public function label(): string
    {
        return match ($this) {
            self::Yes => 'Sim',
            self::No => 'Não',
        };
    }
}
