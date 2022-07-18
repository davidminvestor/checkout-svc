<?php

declare(strict_types=1);

namespace App\Enums;

enum IntegrationsEnum: string
{
    case AMAZON = 'amazon';

    public static function getString(): string
    {
        return implode(',', array_column(self::cases(), 'value'));
    }
}
