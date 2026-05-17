<?php

declare(strict_types=1);

namespace Rugaard\Pollen\Support;

use DateTimeImmutable;
use Exception;

use function array_key_exists;
use function array_map;

/**
 * Class FirestoreDecoder.
 */
class FirestoreDecoder
{
    /**
     * Decode from array.
     *
     * @static
     * @param array $data
     * @return array
     * @throws Exception
     */
    public static function decode(array $data): array
    {
        return (new self)->decodeFirestoreFields(fields: $data);
    }

    /**
     * Decode from JSON.
     *
     * @static
     * @param string $json
     * @return array
     * @throws Exception
     */
    public static function decodeFromJson(string $json): array
    {
        return (new self)->decode(data: json_decode(json: $json, associative: true, flags: JSON_THROW_ON_ERROR));
    }

    /**
     * Decode fields.
     *
     * @param mixed[][] $fields
     * @return mixed[]
     * @throws Exception
     */
    protected function decodeFirestoreFields(array $fields): array
    {
        return array_map(callback: fn (array $value): mixed => $this->decodeFirestoreValue(value: $value), array: $fields);
    }

    /**
     * Decode value.
     *
     * @param array<string, mixed> $value
     * @return mixed
     * @throws Exception
     */
    protected function decodeFirestoreValue(array $value): mixed
    {
        return match (true) {
            array_key_exists(key: 'stringValue', array: $value) => (string) $value['stringValue'],
            array_key_exists(key: 'integerValue', array: $value) => (int) $value['integerValue'],
            array_key_exists(key: 'doubleValue', array: $value) => (float) $value['doubleValue'],
            array_key_exists(key: 'booleanValue', array: $value) => (bool) $value['booleanValue'],
            array_key_exists(key: 'nullValue', array: $value) => null,
            array_key_exists(key: 'timestampValue', array: $value) => new DateTimeImmutable($value['timestampValue']),
            array_key_exists(key: 'arrayValue', array: $value) => array_map(callback: fn (array $item): mixed => $this->decodeFirestoreValue(value: $item), array: $value['arrayValue']['values'] ?? []),
            array_key_exists(key: 'mapValue', array: $value) => $this->decodeFirestoreFields(fields: $value['mapValue']['fields'] ?? []),
            default => throw new Exception(message: 'Unknown value type', code: 500),
        };
    }
}
