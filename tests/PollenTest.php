<?php
declare(strict_types=1);

namespace Rugaard\Pollen\Tests;

use Rugaard\Pollen\Pollen;
use Rugaard\Pollen\Support\MeasurementTypes;
use Rugaard\Pollen\Tests\Support\MockedResponses\MockedResponses;
use Tightenco\Collect\Support\Collection;

/**
 * Class PollenTest.
 *
 * @package Rugaard\Pollen\Tests
 */
class PollenTest extends AbstractTestCase
{
    use MockedResponses;

    /**
     * Test "El" measurement.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testElMeasurement() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockEastMeasurements());

        // Get "El" measurements.
        /* @var $data \Tightenco\Collect\Support\Collection */
        $data = $client->get('copenhagen')->get('measurements')->get(0);

        // Assert "El" details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('id'));
        $this->assertIsInt($data->get('id'));
        $this->assertEquals(1, $data->get('id'));
        $this->assertTrue($data->has('name'));
        $this->assertIsString($data->get('name'));
        $this->assertEquals('El', $data->get('name'));
        $this->assertTrue($data->has('type'));
        $this->assertIsString($data->get('type'));
        $this->assertEquals('pollen', $data->get('type'));
        $this->assertTrue($data->has('value'));
        $this->assertIsInt($data->get('value'));
        $this->assertEquals(0, $data->get('value'));
        $this->assertTrue($data->has('level'));
        $this->assertNull($data->get('level'));
        $this->assertTrue($data->has('inSeason'));
        $this->assertIsBool($data->get('inSeason'));
        $this->assertFalse($data->get('inSeason'));
    }

    /**
     * Test "Hassel" measurement.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testHasselMeasurement() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockEastMeasurements());

        // Get "Hassel" measurements.
        /* @var $data \Tightenco\Collect\Support\Collection */
        $data = $client->get('copenhagen')->get('measurements')->get(1);

        // Assert "Hassel" details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('id'));
        $this->assertIsInt($data->get('id'));
        $this->assertEquals(2, $data->get('id'));
        $this->assertTrue($data->has('name'));
        $this->assertIsString($data->get('name'));
        $this->assertEquals('Hassel', $data->get('name'));
        $this->assertTrue($data->has('type'));
        $this->assertIsString($data->get('type'));
        $this->assertEquals('pollen', $data->get('type'));
        $this->assertTrue($data->has('value'));
        $this->assertIsInt($data->get('value'));
        $this->assertEquals(0, $data->get('value'));
        $this->assertTrue($data->has('level'));
        $this->assertNull($data->get('level'));
        $this->assertTrue($data->has('inSeason'));
        $this->assertIsBool($data->get('inSeason'));
        $this->assertFalse($data->get('inSeason'));
    }

    /**
     * Test "Elm" measurement.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testElmMeasurement() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockEastMeasurements());

        // Get "Elm" measurements.
        /* @var $data \Tightenco\Collect\Support\Collection */
        $data = $client->get('copenhagen')->get('measurements')->get(2);

        // Assert "Elm" details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('id'));
        $this->assertIsInt($data->get('id'));
        $this->assertEquals(4, $data->get('id'));
        $this->assertTrue($data->has('name'));
        $this->assertIsString($data->get('name'));
        $this->assertEquals('Elm', $data->get('name'));
        $this->assertTrue($data->has('type'));
        $this->assertIsString($data->get('type'));
        $this->assertEquals('pollen', $data->get('type'));
        $this->assertTrue($data->has('value'));
        $this->assertIsInt($data->get('value'));
        $this->assertEquals(0, $data->get('value'));
        $this->assertTrue($data->has('level'));
        $this->assertNull($data->get('level'));
        $this->assertTrue($data->has('inSeason'));
        $this->assertIsBool($data->get('inSeason'));
        $this->assertFalse($data->get('inSeason'));
    }

    /**
     * Test "Birk" measurement.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testBirkMeasurement() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockEastMeasurements());

        // Get "Birk" measurements.
        /* @var $data \Tightenco\Collect\Support\Collection */
        $data = $client->get('copenhagen')->get('measurements')->get(3);

        // Assert "Birk" details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('id'));
        $this->assertIsInt($data->get('id'));
        $this->assertEquals(7, $data->get('id'));
        $this->assertTrue($data->has('name'));
        $this->assertIsString($data->get('name'));
        $this->assertEquals('Birk', $data->get('name'));
        $this->assertTrue($data->has('type'));
        $this->assertIsString($data->get('type'));
        $this->assertEquals('pollen', $data->get('type'));
        $this->assertTrue($data->has('value'));
        $this->assertIsInt($data->get('value'));
        $this->assertEquals(0, $data->get('value'));
        $this->assertTrue($data->has('level'));
        $this->assertNull($data->get('level'));
        $this->assertTrue($data->has('inSeason'));
        $this->assertIsBool($data->get('inSeason'));
        $this->assertFalse($data->get('inSeason'));
    }

    /**
     * Test "Græs" measurement.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testGraesMeasurement() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockEastMeasurements());

        // Get "Græs" measurements.
        /* @var $data \Tightenco\Collect\Support\Collection */
        $data = $client->get('copenhagen')->get('measurements')->get(4);

        // Assert "Græs" details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('id'));
        $this->assertIsInt($data->get('id'));
        $this->assertEquals(28, $data->get('id'));
        $this->assertTrue($data->has('name'));
        $this->assertIsString($data->get('name'));
        $this->assertEquals('Græs', $data->get('name'));
        $this->assertTrue($data->has('type'));
        $this->assertIsString($data->get('type'));
        $this->assertEquals('pollen', $data->get('type'));
        $this->assertTrue($data->has('value'));
        $this->assertIsInt($data->get('value'));
        $this->assertEquals(9, $data->get('value'));
        $this->assertTrue($data->has('level'));
        $this->assertIsString($data->get('level'));
        $this->assertEquals('low', $data->get('level'));
        $this->assertTrue($data->has('inSeason'));
        $this->assertIsBool($data->get('inSeason'));
        $this->assertTrue($data->get('inSeason'));
    }

    /**
     * Test "Bynke" measurement.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testBynkeMeasurement() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockEastMeasurements());

        // Get "Bynke" measurements.
        /* @var $data \Tightenco\Collect\Support\Collection */
        $data = $client->get('copenhagen')->get('measurements')->get(5);

        // Assert "Bynke" details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('id'));
        $this->assertIsInt($data->get('id'));
        $this->assertEquals(31, $data->get('id'));
        $this->assertTrue($data->has('name'));
        $this->assertIsString($data->get('name'));
        $this->assertEquals('Bynke', $data->get('name'));
        $this->assertTrue($data->has('type'));
        $this->assertIsString($data->get('type'));
        $this->assertEquals('pollen', $data->get('type'));
        $this->assertTrue($data->has('value'));
        $this->assertIsInt($data->get('value'));
        $this->assertEquals(16, $data->get('value'));
        $this->assertTrue($data->has('level'));
        $this->assertIsString($data->get('level'));
        $this->assertEquals('moderate', $data->get('level'));
        $this->assertTrue($data->has('inSeason'));
        $this->assertIsBool($data->get('inSeason'));
        $this->assertTrue($data->get('inSeason'));
    }

    /**
     * Test "Alternaria" measurement.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testAlternariaMeasurement() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockEastMeasurements());

        // Get "Alternaria" measurements.
        /* @var $data \Tightenco\Collect\Support\Collection */
        $data = $client->get('copenhagen')->get('measurements')->get(6);

        // Assert "Alternaria" details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('id'));
        $this->assertIsInt($data->get('id'));
        $this->assertEquals(44, $data->get('id'));
        $this->assertTrue($data->has('name'));
        $this->assertIsString($data->get('name'));
        $this->assertEquals('Alternaria', $data->get('name'));
        $this->assertTrue($data->has('type'));
        $this->assertIsString($data->get('type'));
        $this->assertEquals('spore', $data->get('type'));
        $this->assertTrue($data->has('value'));
        $this->assertIsInt($data->get('value'));
        $this->assertEquals(490, $data->get('value'));
        $this->assertTrue($data->has('level'));
        $this->assertIsString($data->get('level'));
        $this->assertEquals('high', $data->get('level'));
        $this->assertTrue($data->has('inSeason'));
        $this->assertIsBool($data->get('inSeason'));
        $this->assertTrue($data->get('inSeason'));
    }

    /**
     * Test "Cladosporium" measurement.
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     */
    public function testCladosporiumMeasurement() : void
    {
        // Instantiate Pollen client.
        $client = new Pollen($this->mockEastMeasurements());

        // Get "Cladosporium" measurements.
        /* @var $data \Tightenco\Collect\Support\Collection */
        $data = $client->get('copenhagen')->get('measurements')->get(7);

        // Assert "Cladosporium" details.
        $this->assertInstanceOf(Collection::class, $data);
        $this->assertTrue($data->has('id'));
        $this->assertIsInt($data->get('id'));
        $this->assertEquals(45, $data->get('id'));
        $this->assertTrue($data->has('name'));
        $this->assertIsString($data->get('name'));
        $this->assertEquals('Cladosporium', $data->get('name'));
        $this->assertTrue($data->has('type'));
        $this->assertIsString($data->get('type'));
        $this->assertEquals('spore', $data->get('type'));
        $this->assertTrue($data->has('value'));
        $this->assertIsInt($data->get('value'));
        $this->assertEquals(9552, $data->get('value'));
        $this->assertTrue($data->has('level'));
        $this->assertIsString($data->get('level'));
        $this->assertEquals('high', $data->get('level'));
        $this->assertTrue($data->has('inSeason'));
        $this->assertIsBool($data->get('inSeason'));
        $this->assertTrue($data->get('inSeason'));
    }

    /**
     * Test unsupported pollen type.
     *
     * @return void
     * @throws \ReflectionException
     */
    public function testUnsupportedPollenType() : void
    {
        // Mocked pollen ID.
        $unsupportedPollenId = 12345678;

        // Instantiate measurement types trait.
        $measurementTypesTrait = new class { use MeasurementTypes; };

        // Get supported measurement types.
        /* @var $measurementTypes \Tightenco\Collect\Support\Collection */
        $measurementTypes = $this->invokeMethod($measurementTypesTrait, 'getMeasurementTypes');

        // Assertions.
        $this->assertNull($measurementTypes->where('id', '=', $unsupportedPollenId)->first());
        $this->assertNull($this->invokeMethod($measurementTypesTrait, 'getMeasurementLevel', [$unsupportedPollenId, 100]));
    }
}