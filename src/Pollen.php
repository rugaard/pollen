<?php
declare(strict_types=1);

namespace Rugaard\Pollen;

use DateTime;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use Rugaard\Pollen\Exceptions\InvalidStationException;
use Rugaard\Pollen\Exceptions\ParsingFailedException;
use Rugaard\Pollen\Exceptions\RequestFailedException;
use Rugaard\Pollen\Exceptions\FailedSessionIdException;
use Rugaard\Pollen\Exceptions\SessionIdNotFoundException;
use Rugaard\Pollen\Support\MeasurementTypes;
use Rugaard\Pollen\Support\Stations;
use Throwable;
use Tightenco\Collect\Support\Collection;

/**
 * Class Pollen
 *
 * @package Rugaard\Pollen
 */
class Pollen
{
    use MeasurementTypes, Stations;

    /**
     * Base URL.
     *
     * @const string
     */
    public const POLLEN_BASE_URL = 'https://hoefeber.astma-allergi.dk';

    /**
     * Guzzle Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Client constructor.
     *
     * @param \GuzzleHttp\ClientInterface|null $httpClient
     */
    public function __construct(?ClientInterface $httpClient = null)
    {
        if ($httpClient !== null) {
            $this->setClient($httpClient);
        }
    }

    /**
     * Get session ID for future requests.
     *
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\SessionIdNotFoundException
     * @throws \Rugaard\Pollen\Exceptions\FailedSessionIdException
     */
    protected function getSessionId() :? string
    {
        try {
            // Send options request.
            $this->getClient()->request('options');

            // Get cookies from client.
            $cookies = $this->getClient()->getConfig('cookies');

            // Loop through cookies looking for session ID.
            // If found, return it.
            foreach ($cookies as $cookie) {
                if ($cookie->getName() !== 'JSESSIONID') {
                    continue;
                }

                return $cookie->getValue();
            }

            throw new SessionIdNotFoundException('Session ID not found.', 400);
        } catch (Throwable $e) {
            throw new FailedSessionIdException('Could not retrieve session ID: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get measurements from station.
     *
     * @param string $stationCode
     * @return \Tightenco\Collect\Support\Collection
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Rugaard\Pollen\Exceptions\RequestFailedException
     * @throws \Rugaard\Pollen\Exceptions\InvalidStationException
     */
    public function get(string $stationCode) : Collection
    {
        // Get station by code.
        $station = $this->getStationByCode($stationCode);

        // Validate station ID.
        if ($station === null) {
            throw new InvalidStationException('Invalid station code.', 400);
        }

        try {
            // Get session ID for request.
            $sessionId = $this->getSessionId();

            // Request latest measurements from a specific station.
            $response = $this->getClient()->get('hoefeber/pollen/dagenspollental', [
                'query' => [
                    's' => $sessionId,
                    'station' => $station->get('id'),
                    'p_p_id' => 'pollenbox_WAR_pollenportlet',
                    'p_p_lifecycle' => 2
                ]
            ]);

            // Decode response.
            $data = json_decode((string) $response->getBody(), true);

            // If the decoding procedure failed,
            // we need to abort the parsing.
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ParsingFailedException('Could not JSON decode response. Reason: ' . json_last_error_msg(), 400);
            }

            return Collection::make([
                'station' => $station->get('name'),
                'region' => $station->get('region'),
                'measurements' => Collection::make($data['feed'])->transform(function ($item, $measurementId) {
                    // Get measurement type by ID.
                    $measurement = $this->getMeasurementTypeById($measurementId);

                    // Measurement value.
                    $value = $item['level'] > 0 ? $item['level'] : 0;

                    return Collection::make([
                        'id' => $measurement->get('id'),
                        'name' => $measurement->get('name'),
                        'type' => $measurement->get('type'),
                        'value' => $value,
                        'level' => $this->getMeasurementLevel($measurement->get('id'), $value),
                        'inSeason' => (bool) $item['inSeason']
                    ]);
                })->sortBy('id')->values(),
                'lastUpdated' => DateTime::createFromFormat('d/m/Y', $data['date'])->format('Y-m-d')
            ]);
        } catch (Throwable $e) {
            throw new RequestFailedException('Could not could not retrieve latest pollen measurements: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Set a default client instance.
     *
     * @return void
     */
    protected function defaultClient() : void
    {
        $this->setClient(new GuzzleClient([
            'base_uri' => self::POLLEN_BASE_URL,
            'cookies' => true,
            'headers' => [
                'Accept-Encoding' => 'gzip',
            ]
        ]));
    }

    /**
     * Set client instance.
     *
     * @param  \GuzzleHttp\ClientInterface $client
     * @return $this
     */
    public function setClient(ClientInterface $client) : self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get client instance.
     *
     * @return \GuzzleHttp\ClientInterface|null
     */
    public function getClient() :? ClientInterface
    {
        if ($this->client === null) {
            $this->defaultClient();
        }

        return $this->client;
    }
}