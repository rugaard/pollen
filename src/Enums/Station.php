<?php

declare(strict_types=1);

namespace Rugaard\Pollen\Enums;

/**
 * Class Station.
 */
enum Station: string
{
    case East = 'east';
    case West = 'west';

    /**
     * Get station from ID.
     *
     * @static
     * @param int $id
     * @return self
     */
    public static function fromId(int $id): self
    {
        return match ($id) {
            48 => self::East,
            49 => self::West,
        };
    }

    /**
     * Get name of station.
     *
     * @return string
     */
    public function name(): string
    {
        return match ($this) {
            self::East => 'København',
            self::West => 'Viborg',
        };
    }

    /**
     * Get code of station.
     *
     * @return string
     */
    public function code(): string
    {
        return match ($this) {
            self::East => 'copenhagen',
            self::West => 'viborg',
        };
    }
}
