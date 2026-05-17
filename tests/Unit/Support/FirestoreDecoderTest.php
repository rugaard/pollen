<?php

declare(strict_types=1);

use Rugaard\Pollen\Support\FirestoreDecoder;

describe(description: 'FirestoreDecoder::decodeFromJson()', tests: function () {
    it(description: 'decodes a JSON string and returns the same result as decode()', closure: function () {
        $fields = [
            'date' => ['stringValue' => '2024-04-01'],
            'count' => ['integerValue' => '7'],
        ];

        $fromArray = FirestoreDecoder::decode(data: $fields);
        $fromJson = FirestoreDecoder::decodeFromJson(json: json_encode(value: $fields, flags: JSON_THROW_ON_ERROR));

        expect(value: $fromJson)->toBe(expected: $fromArray);
    });

    it(description: 'returns decoded scalar values from a JSON string', closure: function () {
        $fields = [
            'label' => ['stringValue' => 'hello'],
            'score' => ['integerValue' => '99'],
            'ratio' => ['doubleValue' => 0.5],
            'active' => ['booleanValue' => true],
            'empty' => ['nullValue' => null],
            'ts' => ['timestampValue' => '2024-04-01T00:00:00Z'],
        ];

        $result = FirestoreDecoder::decodeFromJson(json: json_encode(value: $fields, flags: JSON_THROW_ON_ERROR));

        expect(value: $result['label'])->toBe(expected: 'hello')
            ->and(value: $result['score'])->toBe(expected: 99)
            ->and(value: $result['ratio'])->toBe(expected: 0.5)
            ->and(value: $result['active'])->toBeTrue()
            ->and(value: $result['empty'])->toBeNull()
            ->and(value: $result['ts'])->toBeInstanceOf(class: DateTimeImmutable::class);
    });
});
