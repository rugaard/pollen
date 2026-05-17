<?php

declare(strict_types=1);

use Rugaard\Pollen\Enums\Allergen;

it(description: 'returns pollen type for pollen allergens', closure: function (Allergen $allergen) {
    expect(value: $allergen->type())->toBe(expected: 'pollen');
})->with([
    'Alder' => [Allergen::Alder],
    'Hazel' => [Allergen::Hazel],
    'Elm' => [Allergen::Elm],
    'Birch' => [Allergen::Birch],
    'Grass' => [Allergen::Grass],
    'Mugwort' => [Allergen::Mugwort],
]);

it(description: 'returns spore type for spore allergens', closure: function (Allergen $allergen) {
    expect(value: $allergen->type())->toBe(expected: 'spore');
})->with([
    'Alternaria' => [Allergen::Alternaria],
    'Cladosporium' => [Allergen::Cladosporium],
]);

it(description: 'returns correct allergen code', closure: function (Allergen $allergen, string $expected) {
    expect(value: $allergen->code())->toBe(expected: $expected);
})->with([
    'Alder' => [Allergen::Alder, 'alder'],
    'Hazel' => [Allergen::Hazel, 'hazel'],
    'Elm' => [Allergen::Elm, 'elm'],
    'Birch' => [Allergen::Birch, 'birch'],
    'Grass' => [Allergen::Grass, 'grass'],
    'Mugwort' => [Allergen::Mugwort, 'mugwort'],
    'Alternaria' => [Allergen::Alternaria, 'alternaria'],
    'Cladosporium' => [Allergen::Cladosporium, 'cladosporium'],
]);

it(description: 'returns correct Danish name for named cases', closure: function (Allergen $allergen, string $expected) {
    expect(value: $allergen->danishName())->toBe(expected: $expected);
})->with([
    'Alder' => [Allergen::Alder, 'El'],
    'Hazel' => [Allergen::Hazel, 'Hassel'],
    'Birch' => [Allergen::Birch, 'Birk'],
    'Grass' => [Allergen::Grass, 'Græs'],
    'Mugwort' => [Allergen::Mugwort, 'Bynke'],
]);

it(description: 'returns enum name as Danish name for unnamed cases', closure: function (Allergen $allergen) {
    expect(value: $allergen->danishName())->toBe(expected: $allergen->name);
})->with([
    'Elm' => [Allergen::Elm],
    'Alternaria' => [Allergen::Alternaria],
    'Cladosporium' => [Allergen::Cladosporium],
]);

it(description: 'returns correct level thresholds', closure: function (Allergen $allergen, array $expected) {
    expect(value: $allergen->levels())->toBe(expected: $expected);
})->with([
    'Alder' => [Allergen::Alder, ['low' => 10, 'moderate' => 50, 'high' => 200]],
    'Hazel' => [Allergen::Hazel, ['low' => 5, 'moderate' => 15, 'high' => 40]],
    'Elm' => [Allergen::Elm, ['low' => 10, 'moderate' => 50, 'high' => 80]],
    'Birch' => [Allergen::Birch, ['low' => 30, 'moderate' => 100, 'high' => 550]],
    'Grass' => [Allergen::Grass, ['low' => 10, 'moderate' => 50, 'high' => 150]],
    'Mugwort' => [Allergen::Mugwort, ['low' => 10, 'moderate' => 50, 'high' => 60]],
    'Alternaria' => [Allergen::Alternaria, ['low' => 20, 'moderate' => 100, 'high' => 500]],
    'Cladosporium' => [Allergen::Cladosporium, ['low' => 2000, 'moderate' => 6000, 'high' => 7000]],
]);

it(description: 'has correct integer backing values', closure: function (Allergen $allergen, int $value) {
    expect(value: $allergen->value)->toBe(expected: $value);
})->with([
    'Alder' => [Allergen::Alder, 1],
    'Hazel' => [Allergen::Hazel, 2],
    'Elm' => [Allergen::Elm, 4],
    'Birch' => [Allergen::Birch, 7],
    'Grass' => [Allergen::Grass, 28],
    'Mugwort' => [Allergen::Mugwort, 31],
    'Alternaria' => [Allergen::Alternaria, 44],
    'Cladosporium' => [Allergen::Cladosporium, 45],
]);

it(description: 'can be resolved from its integer value', closure: function (int $value, Allergen $expected) {
    expect(value: Allergen::from(value: $value))->toBe(expected: $expected);
})->with([
    [1, Allergen::Alder],
    [2, Allergen::Hazel],
    [4, Allergen::Elm],
    [7, Allergen::Birch],
    [28, Allergen::Grass],
    [31, Allergen::Mugwort],
    [44, Allergen::Alternaria],
    [45, Allergen::Cladosporium],
]);

it(description: 'returns null for unknown allergen id via tryFrom', closure: function () {
    expect(value: Allergen::tryFrom(value: 999))->toBeNull();
});
