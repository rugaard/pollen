<?php

declare(strict_types=1);

use Rugaard\Pollen\Exceptions\ParsingFailedException;

it(description: 'ParsingFailedException extends Exception', closure: function () {
    $e = new ParsingFailedException(message: 'Parsing of response failed', code: 500);
    expect(value: $e)->toBeInstanceOf(class: Exception::class)
        ->and(value: $e->getMessage())->toBe(expected: 'Parsing of response failed')
        ->and(value: $e->getCode())->toBe(expected: 500);
});

it(description: 'ParsingFailedException carries a previous exception', closure: function () {
    $previous = new RuntimeException(message: 'root cause');
    $e = new ParsingFailedException(message: 'Parsing of response failed', previous: $previous);
    expect(value: $e->getPrevious())->toBe(expected: $previous);
});
