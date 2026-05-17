<?php

declare(strict_types=1);

use Rugaard\Pollen\Exceptions\InvalidStationException;

it(description: 'InvalidStationException extends Exception', closure: function () {
    $e = new InvalidStationException(message: 'Invalid station ID: 0', code: 404);
    expect(value: $e)->toBeInstanceOf(class: Exception::class)
        ->and(value: $e->getMessage())->toBe(expected: 'Invalid station ID: 0')
        ->and(value: $e->getCode())->toBe(expected: 404);
});

it(description: 'InvalidStationException carries a previous exception', closure: function () {
    $previous = new RuntimeException(message: 'root cause');
    $e = new InvalidStationException(message: 'Invalid station ID: 0', previous: $previous);
    expect(value: $e->getPrevious())->toBe(expected: $previous);
});
