<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Collection;
use Rugaard\Pollen\Enums\Level;
use Rugaard\Pollen\Enums\Station;
use Rugaard\Pollen\Exceptions\ParsingFailedException;
use Rugaard\Pollen\Exceptions\RequestFailedException;
use Rugaard\Pollen\Pollen;

describe(description: 'constructor and client accessors', tests: function () {
    it(description: 'creates a GuzzleClient by default when no client is given', closure: function () {
        $pollen = new Pollen;
        expect(value: $pollen->getClient())->toBeInstanceOf(class: GuzzleClient::class);
    });

    it(description: 'uses the provided client when one is given', closure: function () {
        $client = Mockery::mock(ClientInterface::class);
        $pollen = new Pollen($client);
        expect(value: $pollen->getClient())->toBe(expected: $client);
    });

    it(description: 'setClient() stores the client and returns $this', closure: function () {
        $pollen = new Pollen;
        $client = Mockery::mock(ClientInterface::class);
        $returned = $pollen->setClient($client);
        expect(value: $returned)->toBe(expected: $pollen)
            ->and(value: $pollen->getClient())->toBe(expected: $client);
    });
});

// ---------------------------------------------------------------------------
// request() — decoding
// ---------------------------------------------------------------------------

describe(description: 'request() decoding of response', tests: function () {
    it(description: 'parses a single-encoded Firestore JSON response', closure: function () {
        $result = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->request();
        expect(value: $result)->toBeInstanceOf(class: Collection::class)->toHaveCount(count: 2)
            ->and(value: $result->has(key: 48))->toBeTrue()
            ->and(value: $result->has(key: 49))->toBeTrue()
            ->and(value: $result->get(key: 48)['date'])->toBe(expected: '2024-04-01');
    });

    it(description: 'parses a double-encoded Firestore JSON response', closure: function () {
        $result = (new Pollen(mockGuzzleClient(status: 200, body: responseValid(doubleEncode: true))))->request();
        expect(value: $result)->toBeInstanceOf(class: Collection::class)->toHaveCount(count: 2);
    });
});

// ---------------------------------------------------------------------------
// request() — Firestore value type decoding
// ---------------------------------------------------------------------------

describe(description: 'Firestore value type decoding via request()', tests: function () {
    beforeEach(closure: function () {
        $this->station = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))
            ->request()
            ->get(key: 48);
    });

    it(description: 'decodes stringValue to a PHP string', closure: function () {
        expect(value: $this->station['date'])->toBeString()->toBe(expected: '2024-04-01');
    });

    it(description: 'decodes integerValue to a PHP integer', closure: function () {
        expect(value: $this->station['extraInteger'])->toBeInt()->toBe(expected: 42);
    });

    it(description: 'decodes doubleValue to a PHP float', closure: function () {
        expect(value: $this->station['extraDouble'])->toBeFloat()->toBe(expected: 1.5);
    });

    it(description: 'decodes booleanValue to a PHP bool', closure: function () {
        expect(value: $this->station['extraBoolean'])->toBeBool()->toBeTrue();
    });

    it(description: 'decodes nullValue to PHP null', closure: function () {
        expect(value: $this->station['extraNull'])->toBeNull();
    });

    it(description: 'decodes timestampValue to a DateTimeImmutable', closure: function () {
        expect(value: $this->station['extraTimestamp'])->toBeInstanceOf(class: DateTimeImmutable::class)
            ->and(value: $this->station['extraTimestamp']->format(format: 'Y-m-d'))->toBe(expected: '2024-04-01');
    });

    it(description: 'decodes arrayValue to a PHP array', closure: function () {
        expect(value: $this->station['extraArray'])->toBeArray()->toBe(expected: ['a', 'b']);
    });

    it(description: 'decodes mapValue to a PHP array (recursive)', closure: function () {
        // The `data` field is a Firestore mapValue; decoding yields an array of allergen entries.
        expect(value: $this->station['data'])->toBeArray()
            ->and(value: $this->station['data'][1])->toBeArray()->toHaveKey(key: 'inSeason');
    });

    it(description: 'throws ParsingFailedException for an unknown Firestore value type', closure: function () {
        (new Pollen(mockGuzzleClient(status: 200, body: responseUnknownType())))->request();
    })->throws(exception: ParsingFailedException::class, exceptionMessage: 'Failed to parse response body');
});

// ---------------------------------------------------------------------------
// request() — errors
// ---------------------------------------------------------------------------

describe(description: 'request() errors', tests: function () {
    it(description: 'throws RequestFailedException when the HTTP status is not 200', closure: function () {
        (new Pollen(mockGuzzleClient(status: 503, body: '')))->request();
    })->throws(exception: RequestFailedException::class, exceptionMessage: 'Request failed with status code: 503');

    it(description: 'throws RequestFailedException when Guzzle throws a network error', closure: function () {
        $mock = Mockery::mock(ClientInterface::class);
        $mock->shouldReceive('request')
            ->andThrow(new ConnectException('Network error', new Request('GET', 'test')));
        (new Pollen($mock))->request();
    })->throws(exception: RequestFailedException::class, exceptionMessage: 'Failed to request pollen data');

    it(description: 'throws ParsingFailedException when the response body is not valid JSON', closure: function () {
        (new Pollen(mockGuzzleClient(status: 200, body: 'not-json')))->request();
    })->throws(exception: ParsingFailedException::class, exceptionMessage: 'Failed to parse response body');
});

// ---------------------------------------------------------------------------
// get() — allergen level classification
// ---------------------------------------------------------------------------

describe(description: 'get() allergen level classification', tests: function () {
    beforeEach(closure: function () {
        $this->east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East);
    });

    it(description: 'classifies level 0 as Unknown', closure: function () {
        expect(value: $this->east->get(key: 'alder')->get(key: 'level'))->toBe(expected: Level::Unknown)
            ->and(value: $this->east->get(key: 'alder')->get(key: 'value'))->toBe(expected: 0);
    });

    it(description: 'classifies a level below the low threshold as Low', closure: function () {
        expect(value: $this->east->get(key: 'hazel')->get(key: 'level'))->toBe(expected: Level::Low)
            ->and(value: $this->east->get(key: 'hazel')->get(key: 'value'))->toBe(expected: 3);
    });

    it(description: 'classifies a level in the moderate range as Moderate', closure: function () {
        expect(value: $this->east->get(key: 'elm')->get(key: 'level'))->toBe(expected: Level::Moderate)
            ->and(value: $this->east->get(key: 'elm')->get(key: 'value'))->toBe(expected: 25);
    });

    it(description: 'classifies a level in the high range as High', closure: function () {
        expect(value: $this->east->get(key: 'birch')->get(key: 'level'))->toBe(expected: Level::High)
            ->and(value: $this->east->get(key: 'birch')->get(key: 'value'))->toBe(expected: 200);
    });

    it(description: 'classifies a level at or above the high threshold as VeryHigh', closure: function () {
        expect(value: $this->east->get(key: 'grass')->get(key: 'level'))->toBe(expected: Level::VeryHigh)
            ->and(value: $this->east->get(key: 'grass')->get(key: 'value'))->toBe(expected: 200);
    });
});

// ---------------------------------------------------------------------------
// get() — in-season / out-of-season handling
// ---------------------------------------------------------------------------

describe(description: 'get() in/out of season handling', tests: function () {
    it(description: 'returns null for allergens not in season', closure: function () {
        $east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East);
        expect(value: $east->get(key: 'mugwort'))->toBeNull();
    });

    it(description: 'returns null for unknown allergen ids', closure: function () {
        $east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East);
        expect(value: $east->get(key: 99))->toBeNull();
    });

    it(description: 'filters out-of-season entries when onlyInSeason is true', closure: function () {
        $east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East, onlyInSeason: true);
        expect(value: $east->has(key: 'mugwort'))->toBeFalse()
            ->and(value: $east->has(key: 99))->toBeFalse()
            ->and(value: $east->has(key: 'alder'))->toBeTrue();
    });

    it(description: 'keeps null entries when onlyInSeason is false', closure: function () {
        $east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East, onlyInSeason: false);
        expect(value: $east->has(key: 'mugwort'))->toBeTrue()
            ->and(value: $east->has(key: 99))->toBeTrue();
    });
});

// ---------------------------------------------------------------------------
// get() — spore vs pollen predictions
// ---------------------------------------------------------------------------

describe(description: 'get() spore vs pollen allergen handling', tests: function () {
    it(description: 'sets predictions to null for spore allergens', closure: function () {
        $east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East);
        expect(value: $east->get(key: 'alternaria')->get(key: 'predictions'))->toBeNull()
            ->and(value: $east->get(key: 'cladosporium')->get(key: 'predictions'))->toBeNull();
    });

    it(description: 'returns a predictions Collection for pollen allergens', closure: function () {
        $east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East);
        $predictions = $east->get(key: 'hazel')->get(key: 'predictions');
        expect(value: $predictions)->toBeInstanceOf(class: Collection::class)->toHaveCount(count: 2)
            ->and(value: $predictions->first()['date'])->toBe(expected: '2024-04-02')
            ->and(value: $predictions->first()['level'])->toBe(expected: Level::Low)
            ->and(value: $predictions->last()['date'])->toBe(expected: '2024-04-03')
            ->and(value: $predictions->last()['level'])->toBe(expected: Level::Moderate);
    });

    it(description: 'maps all prediction level values correctly', closure: function () {
        $east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East);
        expect(value: $east->get(key: 'birch')->get(key: 'predictions')->first()['level'])->toBe(expected: Level::High)
            ->and(value: $east->get(key: 'grass')->get(key: 'predictions')->first()['level'])->toBe(expected: Level::VeryHigh);
    });

    it(description: 'attaches the station date to each measurement', closure: function () {
        $east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East);
        expect(value: $east->get(key: 'hazel')->get(key: 'date'))->toBe(expected: '2024-04-01');
    });
});

// ---------------------------------------------------------------------------
// get() — station filtering
// ---------------------------------------------------------------------------

describe(description: 'get() station filtering', tests: function () {
    it(description: 'returns all stations when no station is specified', closure: function () {
        $result = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get();
        expect(value: $result)->toBeInstanceOf(class: Collection::class)
            ->and(value: $result->has(key: 'east'))
            ->toBeTrue()
            ->and(value: $result->has(key: 'west'))
            ->toBeTrue();
    });

    it(description: 'returns only the requested station', closure: function () {
        $result = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::West);
        expect(value: $result->has(key: 'birch'))->toBeTrue()
            ->and(value: $result->has(key: 'alder'))->toBeFalse();
    });

    it(description: 'returns an empty collection when the requested station has no data', closure: function () {
        $emptyFixture = json_encode(value: ['fields' => []], flags: JSON_THROW_ON_ERROR);
        $result = (new Pollen(mockGuzzleClient(status: 200, body: $emptyFixture)))->get(station: Station::East);
        expect(value: $result)->toBeInstanceOf(class: Collection::class)->toHaveCount(count: 0);
    });
});

// ---------------------------------------------------------------------------
// get() — exception safety
// ---------------------------------------------------------------------------

describe(description: 'get() exception safety', tests: function () {
    it(description: 'throws RequestFailedException when the HTTP request fails', closure: function () {
        $mock = Mockery::mock(ClientInterface::class);
        $mock->shouldReceive('request')->andThrow(new ConnectException('Network error', new Request('GET', 'test')));
        (new Pollen($mock))->get();
    })->throws(exception: RequestFailedException::class, exceptionMessage: 'Failed to request pollen data');

    it(description: 'throws ParsingFailedException when the response cannot be parsed', closure: function () {
        (new Pollen(mockGuzzleClient(status: 200, body: 'not-json')))->get();
    })->throws(exception: ParsingFailedException::class, exceptionMessage: 'Failed to parse response body');
});

// ---------------------------------------------------------------------------
// get() — result ordering
// ---------------------------------------------------------------------------

describe(description: 'get() result ordering', tests: function () {
    it(description: 'returns allergen measurements sorted by key', closure: function () {
        $east = (new Pollen(mockGuzzleClient(status: 200, body: responseValid())))->get(station: Station::East);
        $keys = $east->keys()->all();
        $sorted = $keys;
        sort(array: $sorted);
        expect(value: $keys)->toBe(expected: $sorted);
    });
});
