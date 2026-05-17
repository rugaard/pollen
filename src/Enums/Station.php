<?php

declare(strict_types=1);

namespace Rugaard\Pollen\Enums;

use Rugaard\Pollen\Exceptions\InvalidStationException;

/**
 * Class Station.
 */
enum Station: string
{
    case East = 'east';
    case West = 'west';

    /**
     * Get station from ID or throw exception.
     *
     * @static
     * @param int $id
     * @return self
     * @throws InvalidStationException
     */
    public static function fromId(int $id): self
    {
        return match ($id) {
            48 => self::East,
            49 => self::West,
            default => throw new InvalidStationException(message: "Invalid station ID: {$id}", code: 404),
        };
    }

    /**
     * Get station from ID or return null.
     *
     * @static
     * @param int $id
     * @return self|null
     */
    public static function tryFromId(int $id): ?self
    {
        try {
            return self::fromId(id: $id);
        } catch (InvalidStationException) {
            return null;
        }
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
