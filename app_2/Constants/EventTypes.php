<?php

namespace App\Constants;

final class EventTypes
{
    public const PERFORMANCE = 'performance';
    public const GYM = 'gym';
    public const MEDICAL = 'medical';

    public const LIST = [
        self::PERFORMANCE,
        self::GYM,
        self::MEDICAL,
    ];

    public static function getOptions()
    {
        return array_combine(self::LIST, self::LIST);
    }
}
