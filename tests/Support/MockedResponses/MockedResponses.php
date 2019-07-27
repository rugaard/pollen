<?php
declare(strict_types=1);

namespace Rugaard\Pollen\Tests\Support\MockedResponses;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

/**
 * Trait MockedResponses
 *
 * @package Rugaard\DMI\Tests\Support\MockedResponses
 */
trait MockedResponses
{
    /**
     * Mocked east measurements response.
     *
     * @return \GuzzleHttp\Client
     */
    private function mockEastMeasurements() : GuzzleClient
    {
        return $this->createMockedGuzzleClient([
            new GuzzleResponse(200, [], null),
            new GuzzleResponse(200, [], file_get_contents(__DIR__ . '/JSON/East.json')),
        ]);
    }

    /**
     * Mocked west measurements response.
     *
     * @return \GuzzleHttp\Client
     */
    private function mockWestMeasurements() : GuzzleClient
    {
        return $this->createMockedGuzzleClient([
            new GuzzleResponse(200, [], null),
            new GuzzleResponse(200, [], file_get_contents(__DIR__ . '/JSON/West.json')),
        ]);
    }

    /**
     * Mock no cookies response.
     *
     * @return \GuzzleHttp\Client
     */
    private function mockNoCookiesRequest() : GuzzleClient
    {
        return $this->createMockedGuzzleClient([
            new GuzzleResponse(200, [], null),
            new GuzzleResponse(200, [], null),
        ], false);
    }

    /**
     * Mock "missing session ID" response.
     *
     * @return \GuzzleHttp\Client
     */
    private function mockMissingSessionIdRequest() : GuzzleClient
    {
        return $this->createMockedGuzzleClient([
            new GuzzleResponse(200, [], null),
            new GuzzleResponse(200, [], null),
        ], true, false);
    }

    /**
     * Mock invalid JSON response.
     *
     * @return \GuzzleHttp\Client
     */
    private function mockInvalidJsonRequest() : GuzzleClient
    {
        return $this->createMockedGuzzleClient([
            new GuzzleResponse(200, [], null),
            new GuzzleResponse(200, [], 'This is not JSON.'),
        ]);
    }

    /**
     * Mock "not found" response.
     *
     * @return \GuzzleHttp\Client
     */
    private function mockNotFoundRequest() : GuzzleClient
    {
        return $this->createMockedGuzzleClient([
            new GuzzleResponse(200, [], null),
            new GuzzleResponse(404, [], null),
        ]);
    }
}