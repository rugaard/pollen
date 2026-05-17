<?php

declare(strict_types=1);

namespace Rugaard\Pollen\Enums;

/**
 * Class Level.
 */
enum Level: int
{
    case Unknown = 0;
    case Low = 1;
    case Moderate = 2;
    case High = 3;
    case VeryHigh = 4;

    /**
     * Get code of level.
     *
     * @return string
     */
    public function code(): string
    {
        return match ($this) {
            self::Unknown => 'unknown',
            self::Low => 'low',
            self::Moderate => 'moderate',
            self::High => 'high',
            self::VeryHigh => 'very-high',
        };
    }

    /**
     * Get the Danish name of level.
     *
     * @return string
     */
    public function danishName(): string
    {
        return match ($this) {
            self::Unknown => 'ukendt',
            self::Low => 'lavt',
            self::Moderate => 'moderat',
            self::High => 'højt',
            self::VeryHigh => 'meget højt',
        };
    }
}
