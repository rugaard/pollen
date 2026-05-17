<?php
declare(strict_types=1);

namespace Rugaard\Pollen;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Rugaard\Pollen\Enums\Allergen;
use Rugaard\Pollen\Enums\Level;
use Rugaard\Pollen\Enums\Station;
use Rugaard\Pollen\Exceptions\ParsingFailedException;
use Rugaard\Pollen\Exceptions\RequestFailedException;
use Rugaard\Pollen\Support\FirestoreDecoder;
use Throwable;

use function date_parse;
use function is_string;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * Class Pollen
 *
 * @package Rugaard\Pollen
 */
class Pollen
{
    /**
     * Client version.
     *
     * @const string
     */
    public const string VERSION = '1.0';

    /**
     * Guzzle Client instance.
     *
     * @var GuzzleClientInterface
     */
    protected GuzzleClientInterface $client;

    /**
     * Client constructor.
     *
     * @param GuzzleClientInterface|null $client
     */
    public function __construct(?GuzzleClientInterface $client = null)
    {
        $this->setClient(client: $client ?? $this->defaultClient());
    }

    /**
     * Get latest measurements and predictions.
     *
     * @param Station|null $station
     * @param bool $onlyInSeason
     * @return Collection
     * @throws ParsingFailedException
     * @throws RequestFailedException
     */
    public function get(?Station $station = null, bool $onlyInSeason = false): Collection
    {
        // Request latest measurements and predictions.
        $data = $this->request()->mapWithKeys(callback: function (array $stationData, int $stationId) use ($onlyInSeason): array {
            // Get station from station ID.
            $currentStation = Station::fromId(id: $stationId);

            // Parse measurements for each station.
            $measurements = Collection::make(items: $stationData['data'] ?? [])->mapWithKeys(callback: function (array $allergenData, int $allergenId) use ($stationData): array {
                // Get Allergen from ID.
                $allergen = Allergen::tryFrom(value: $allergenId);

                // If the Allergen is not found or the season is not active,
                // we're going to set the value to null and move on.
                if ($allergen === null || $allergenData['inSeason'] === false) {
                    return [$allergen?->code() ?? $allergenId => null];
                }

                return [$allergen->code() => Collection::make(items: [
                    'date' => $stationData['date'],
                    'value' => $allergenData['level'],
                    'level' => match (true) {
                        $allergenData['level'] <= 0 => Level::Unknown,
                        $allergenData['level'] < $allergen->levels()['low'] => Level::Low,
                        $allergenData['level'] < $allergen->levels()['moderate'] => Level::Moderate,
                        $allergenData['level'] < $allergen->levels()['high'] => Level::High,
                        default => Level::VeryHigh,
                    },
                    'predictions' => $allergen->type() === 'spore' ? null : Collection::make(items: $allergenData['overrides'] ?? [])->map(callback: function ($prediction, $index) use ($allergenData): array {
                        $dates = Collection::make(items: $allergenData['predictions'] ?? [])->keys()->sort()->values();
                        return [
                            'date' => $dates->get(key: $index),
                            'level' => Level::from(value: (int) $prediction)
                        ];
                    })
                ])];
            });

            // Support removal of Allergens that are not in season.
            if ($onlyInSeason) {
                $measurements = $measurements->filter(callback: fn ($measurement): bool => $measurement !== null);
            }

            return [$currentStation->value => $measurements->sortKeys()];
        });

        // Return measurements for specific station or all stations.
        return $station instanceof Station ? $data->get(key: $station->value, default: Collection::make()) : $data;
    }

    /**
     * Send request to API and return decoded response.
     *
     * @return Collection
     * @throws RequestFailedException
     * @throws ParsingFailedException
     */
    public function request() : Collection
    {
        try {
            // Request latest measurements and predictions from API.
            $response = $this->getClient()->request(method: 'GET', uri: 'https://www.astma-allergi.dk/umbraco/api/pollenapi/getpollenfeed');

            // Validate response.
            if ($response->getStatusCode() !== 200) {
                throw new RequestFailedException(message: 'Request failed with status code: ' . $response->getStatusCode());
            }

            try {
                // Decode initial JSON response.
                $data = json_decode(json: $response->getBody()->getContents(), associative: true, flags: JSON_THROW_ON_ERROR);

                // If the response was double-encoded, decode it once more.
                if (is_string($data)) {
                    $data = json_decode(json: $data, associative: true, flags: JSON_THROW_ON_ERROR);
                }

                return Collection::make(items: FirestoreDecoder::decode(data: $data['fields'] ?? []));
            } catch (Throwable $e) {
                throw new ParsingFailedException(message: 'Failed to parse response body: ' . $e->getMessage(), code: $e->getCode(), previous: $e);
            }
        } catch (GuzzleException $guzzleException) {
            throw new RequestFailedException(message: 'Failed to request pollen data: ' . $guzzleException->getMessage(), code: $guzzleException->getCode(), previous: $guzzleException);
        }
    }

    /**
     * Set a default client instance.
     *
     * @return GuzzleClient
     */
    protected function defaultClient() : GuzzleClient
    {
        return new GuzzleClient([
            'headers' => [
                'Accept' => 'application/json',
                'Accept-Encoding' => 'br;q=1.0, gzip;q=0.8, *;q=0.5',
                'User-Agent' => 'Rugaard Pollen/' . self::VERSION . ' (https://github.com/rugaard/pollen) PHP/' . PHP_VERSION
            ]
        ]);
    }

    /**
     * Set client instance.
     *
     * @param GuzzleClientInterface $client
     * @return $this
     */
    public function setClient(GuzzleClientInterface $client) : self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get client instance.
     *
     * @return GuzzleClientInterface
     */
    public function getClient(): GuzzleClientInterface
    {
        return $this->client;
    }
}
