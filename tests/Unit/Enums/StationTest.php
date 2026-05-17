<?php

declare(strict_types=1);

use Rugaard\Pollen\Enums\Station;
use Rugaard\Pollen\Exceptions\InvalidStationException;

it(description:'resolves station East from id 48', closure: function () {
    expect(value: Station::fromId(id: 48))->toBe(expected: Station::East);
});

it(description:'resolves station West from id 49', closure: function () {
    expect(value: Station::fromId(id: 49))->toBe(expected: Station::West);
});

it(description:'throws InvalidStationException for an unknown station id', closure: function () {
    Station::fromId(id: 0);
})->throws(exception: InvalidStationException::class, exceptionMessage: 'Invalid station ID: 0');

it(description:'InvalidStationException carries the station id in the message', closure: function () {
    expect(value: fn () => Station::fromId(id: 999))->toThrow(exception: InvalidStationException::class, exceptionMessage: 'Invalid station ID: 999');
});

it(description:'InvalidStationException has code 404', closure: function () {
    try {
        Station::fromId(id: 0);
    } catch (InvalidStationException $e) {
        expect(value: $e->getCode())->toBe(expected: 404);
    }
});

it(description:'tryFromId() returns station East for id 48', closure: function () {
    expect(value: Station::tryFromId(id: 48))->toBe(expected: Station::East);
});

it(description:'tryFromId() returns station West for id 49', closure: function () {
    expect(value: Station::tryFromId(id: 49))->toBe(expected: Station::West);
});

it(description:'tryFromId() returns null for an unknown station id', closure: function () {
    expect(value: Station::tryFromId(id: 0))->toBeNull();
});

it(description:'returns correct station name', closure: function (Station $station, string $expected) {
    expect(value: $station->name())->toBe(expected: $expected);
})->with([
    'East' => [Station::East, 'København'],
    'West' => [Station::West, 'Viborg'],
]);

it(description:'returns correct station code', closure: function (Station $station, string $expected) {
    expect(value: $station->code())->toBe(expected: $expected);
})->with([
    'East' => [Station::East, 'copenhagen'],
    'West' => [Station::West, 'viborg'],
]);

it(description:'has correct string backing values', closure: function (Station $station, string $value) {
    expect(value: $station->value)->toBe(expected: $value);
})->with([
    'East' => [Station::East, 'east'],
    'West' => [Station::West, 'west'],
]);

it(description:'can be resolved from its string value', closure: function (string $value, Station $expected) {
    expect(value: Station::from(value: $value))->toBe(expected: $expected);
})->with([
    ['east', Station::East],
    ['west', Station::West],
]);
