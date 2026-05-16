<?php

declare(strict_types=1);

namespace Rugaard\Pollen\Enums;

/**
 * Class Allergen.
 */
enum Allergen: int
{
    case Alder = 1;
    case Hazel = 2;
    case Elm = 4;
    case Birch = 7;
    case Grass = 28;
    case Mugwort = 31;
    case Alternaria = 44;
    case Cladosporium = 45;

    /**
     * Get type of allergen.
     *
     * @return string
     */
    public function type(): string
    {
        return match ($this) {
            self::Alder, self::Hazel, self::Elm, self::Birch, self::Grass, self::Mugwort => 'pollen',
            self::Alternaria, self::Cladosporium => 'spore',
        };
    }

    /**
     * Get code of allergen.
     *
     * @return string
     */
    public function code(): string
    {
        return match ($this) {
            self::Alder => 'alder',
            self::Hazel => 'hazel',
            self::Elm => 'elm',
            self::Birch => 'birch',
            self::Grass => 'grass',
            self::Mugwort => 'mugwort',
            self::Alternaria => 'alternaria',
            self::Cladosporium => 'cladosporium',
        };
    }

    /**
     * Get the Danish name of allergen.
     *
     * @return string
     */
    public function danishName(): string
    {
        return match ($this) {
            self::Alder => 'El',
            self::Hazel => 'Hassel',
            self::Birch => 'Birk',
            self::Grass => 'Græs',
            self::Mugwort => 'Bynke',
            default => $this->name,
        };
    }

    /**
     * Get allergen levels.
     *
     * @return int[]
     */
    public function levels(): array
    {
        return match ($this) {
            self::Alder => ['low' => 10, 'moderate' => 50, 'high' => 200],
            self::Hazel => ['low' => 5, 'moderate' => 15, 'high' => 40],
            self::Elm => ['low' => 10, 'moderate' => 50, 'high' => 80],
            self::Birch => ['low' => 30, 'moderate' => 100, 'high' => 550],
            self::Grass => ['low' => 10, 'moderate' => 50, 'high' => 150],
            self::Mugwort => ['low' => 10, 'moderate' => 50, 'high' => 60],
            self::Alternaria => ['low' => 20, 'moderate' => 100, 'high' => 500],
            self::Cladosporium => ['low' => 2000, 'moderate' => 6000, 'high' => 7000],
        };
    }
}
