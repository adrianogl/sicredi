<?php

namespace App\Enums;

enum VoteOption: string
{
    case Yes = 'Yes';
    case No = 'No';

    /** @return list<string> */
    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}
