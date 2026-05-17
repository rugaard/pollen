<?php

declare(strict_types=1);

use Rugaard\Pollen\Exceptions\RequestFailedException;

it(description: 'RequestFailedException extends Exception', closure: function () {
    $e = new RequestFailedException(message: 'Request failed', code: 500);
    expect(value: $e)->toBeInstanceOf(class: Exception::class)
        ->and(value: $e->getMessage())->toBe(expected: 'Request failed')
        ->and(value: $e->getCode())->toBe(expected: 500);
});

it(description: 'RequestFailedException carries a previous exception', closure: function () {
    $previous = new RuntimeException(message: 'root cause');
    $e = new RequestFailedException(message: 'Request failed', previous: $previous);
    expect(value: $e->getPrevious())->toBe(expected: $previous);
});
