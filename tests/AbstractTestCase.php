<?php
declare(strict_types=1);

namespace Rugaard\Pollen\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Handler\MockHandler as GuzzleMockHandler;
use GuzzleHttp\HandlerStack as GuzzleHandlerStack;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Class AbstractTestCase
 *
 * @package Rugaard\Pollen\Tests
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * Create a Guzzle Client with mocked responses and cookies.
     *
     * @param  array $responses
     * @param  bool  $includeCookies
     * @param  bool  $includeSessionId
     * @return \GuzzleHttp\Client
     */
    protected function createMockedGuzzleClient(array $responses, bool $includeCookies = true, bool $includeSessionId = true) : GuzzleClient
    {
        return new GuzzleClient([
            'cookies' => $includeCookies ? $this->getMockedCookieJar($includeSessionId) : false,
            'handler' => GuzzleHandlerStack::create(
                new GuzzleMockHandler($responses)
            ),
        ]);
    }

    /**
     * Get Mocked Cookie Jar.
     *
     * @param  bool $includeSessionId
     * @return \GuzzleHttp\Cookie\CookieJar
     */
    protected function getMockedCookieJar(bool $includeSessionId = true) : CookieJar
    {
        return CookieJar::fromArray(array_merge(
            [
            'COOKIE_SUPPORT' => true,
            'GUEST_LANGUAGE_ID' => 'da_DK'
            ],
            $includeSessionId ? ['JSESSIONID' => 'DA811D08A46432B0452473F18A0238BC'] : []
        ), '/');
    }

    /**
     * Call protected/private method of a class.
     *
     * @param  object &$object
     * @param  string $methodName
     * @param  array  $parameters
     * @return mixed
     * @throws \ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    /**
     * Tear down test case after all tests are done.
     *
     * @return void
     */
    public function tearDown() : void
    {
        Mockery::close();
    }
}