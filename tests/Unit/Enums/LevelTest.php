<?php

declare(strict_types=1);

use Rugaard\Pollen\Enums\Level;

it(description: 'returns correct level code', closure: function (Level $level, string $expected) {
    expect(value: $level->code())->toBe(expected: $expected);
})->with([
    'Unknown' => [Level::Unknown, 'unknown'],
    'Low' => [Level::Low, 'low'],
    'Moderate' => [Level::Moderate, 'moderate'],
    'High' => [Level::High, 'high'],
    'VeryHigh' => [Level::VeryHigh, 'very-high'],
]);

it(description: 'returns correct Danish name', closure: function (Level $level, string $expected) {
    expect(value: $level->danishName())->toBe(expected: $expected);
})->with([
    'Unknown' => [Level::Unknown, 'ukendt'],
    'Low' => [Level::Low, 'lavt'],
    'Moderate' => [Level::Moderate, 'moderat'],
    'High' => [Level::High, 'højt'],
    'VeryHigh' => [Level::VeryHigh, 'meget højt'],
]);

it(description: 'has correct integer backing values', closure: function (Level $level, int $value) {
    expect(value: $level->value)->toBe(expected: $value);
})->with([
    'Unknown' => [Level::Unknown, 0],
    'Low' => [Level::Low, 1],
    'Moderate' => [Level::Moderate, 2],
    'High' => [Level::High, 3],
    'VeryHigh' => [Level::VeryHigh, 4],
]);

it(description: 'can be resolved from its integer value', closure: function (int $value, Level $expected) {
    expect(value: Level::from(value: $value))->toBe(expected: $expected);
})->with([
    [0, Level::Unknown],
    [1, Level::Low],
    [2, Level::Moderate],
    [3, Level::High],
    [4, Level::VeryHigh],
]);
