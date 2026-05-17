<?php

declare(strict_types=1);

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

afterEach(closure: function () {
    Mockery::close();
});

/**
 * Build a mock Guzzle client that returns a fixed HTTP response.
 *
 * @param int $status
 * @param string $body
 * @return ClientInterface
 */
function mockGuzzleClient(int $status, string $body): ClientInterface
{
    $mock = Mockery::mock(ClientInterface::class);
    $mock->shouldReceive('request')->andReturn(new Response($status, [], $body));
    return $mock;
}

/**
 * Return a mocked valid Pollen response.
 *
 * @param bool $doubleEncode
 * @return string
 * @throws JsonException
 */
function responseValid(bool $doubleEncode = false): string
{
    $json = file_get_contents(filename: __DIR__ . '/mocks/response_valid.json');
    return $doubleEncode ? json_encode(value: $json, flags: JSON_THROW_ON_ERROR) : $json;
}

/**
 * Return a mocked invalid Pollen response with an unknown type.
 *
 * @return string
 */
function responseUnknownType(): string
{
    return file_get_contents(filename: __DIR__ . '/mocks/response_unknown_type.json');
}
