<?php
declare(strict_types=1);

namespace Rugaard\Pollen\Tests;

use GuzzleHttp\ClientInterface;
use Rugaard\Pollen\Exceptions\FailedSessionIdException;
use Rugaard\Pollen\Exceptions\InvalidStationException;
use Rugaard\Pollen\Exceptions\RequestFailedException;
use Rugaard\Pollen\Pollen;
use Rugaard\Pollen\Tests\Support\MockedResponses\MockedResponses;
use Tightenco\Collect\Support\Collection;

/**
 * Class ClientTest.
 *
 * @package Rugaard\Pollen\Tests
 */
class ClientTest extends AbstractTestCase
{
    use MockedResponses;

    /**
     * Test "east" measurements.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testEastMeasurements() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockEastMeasurements());

        // Get measurements from Copenhagen (East).
        $data = $client->get('copenhagen');

        // Assert basic details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('station'));
        $this->assertIsString($data->get('station'));
        $this->assertEquals('København', $data->get('station'));
        $this->assertTrue($data->has('region'));
        $this->assertIsString($data->get('region'));
        $this->assertEquals('east', $data->get('region'));
        $this->assertTrue($data->has('measurements'));
        $this->assertInstanceOf(Collection::class, $data->get('measurements'));
        $this->assertCount(8, $data->get('measurements'));
        $this->assertTrue($data->has('lastUpdated'));
        $this->assertIsString($data->get('lastUpdated'));
        $this->assertEquals('2019-07-25', $data->get('lastUpdated'));
    }

    /**
     * Test "west" measurements.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testWestMeasurements() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockWestMeasurements());

        // Get measurements from Viborg (west).
        $data = $client->get('viborg');

        // Assert basic details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('station'));
        $this->assertIsString($data->get('station'));
        $this->assertEquals('Viborg', $data->get('station'));
        $this->assertTrue($data->has('region'));
        $this->assertIsString($data->get('region'));
        $this->assertEquals('west', $data->get('region'));
        $this->assertTrue($data->has('measurements'));
        $this->assertInstanceOf(Collection::class, $data->get('measurements'));
        $this->assertCount(8, $data->get('measurements'));
        $this->assertTrue($data->has('lastUpdated'));
        $this->assertIsString($data->get('lastUpdated'));
        $this->assertEquals('2019-07-25', $data->get('lastUpdated'));
    }

    /**
     * Test default client.
     *
     * @return void
     */
    public function testDefaultClient() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen;

        // Get underlying client.
        $underlyingClient = $client->getClient();
        // Assertions.
        $this->assertInstanceOf(ClientInterface::class, $underlyingClient);
        $this->assertEquals(Pollen::POLLEN_BASE_URL, $underlyingClient->getConfig('base_uri'));
    }

    /**
     * Test invalid station code.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testInvalidStation() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen;

        // Expect exception.
        $this->expectException(InvalidStationException::class);
        $this->expectExceptionMessage('Invalid station code.');

        // Send request.
        $client->get('unknown-station');
    }

    /**
     * Test missing session ID.
     *
     * @return void
     * @throws \ReflectionException
     */
    public function testNoCookies() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockNoCookiesRequest());

        // Expect exception.
        $this->expectException(FailedSessionIdException::class);

        // Send request.
        $this->invokeMethod($client, 'getSessionId');
    }

    /**
     * Test missing session ID.
     *
     * @return void
     * @throws \ReflectionException
     */
    public function testMissingSessionId() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockMissingSessionIdRequest());

        // Expect exception.
        $this->expectException(FailedSessionIdException::class);
        $this->expectExceptionMessage('Could not retrieve session ID: Session ID not found.');

        // Send request.
        $this->invokeMethod($client, 'getSessionId');
    }

    /**
     * Test invalid JSON response.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testInvalidJson() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockInvalidJsonRequest());

        // Expect exception.
        $this->expectException(RequestFailedException::class);
        $this->expectExceptionMessage('Could not could not retrieve latest pollen measurements: Could not JSON decode response. Reason: Syntax error');

        // Request data.
        $client->get('copenhagen');
    }

    /**
     * Test failed request.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testFailedRequest() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockNotFoundRequest());

        // Expect exception.
        $this->expectException(RequestFailedException::class);

        // Send request.
        $client->get('copenhagen');
    }

    /**
     * Test Guzzle failed request.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testGuzzleFailedRequest() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockNotFoundRequest());

        // Expect exception.
        $this->expectException(RequestFailedException::class);

        // Send request.
        $client->get('copenhagen');
    }
}