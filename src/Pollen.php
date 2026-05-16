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
use Rugaard\Pollen\Support\MeasurementTypes;
use Rugaard\Pollen\Support\Stations;
use Throwable;

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
     * @return Collection
     */
    public function get(?Station $station = null, bool $onlyInSeason = false): Collection
    {
        try {
            // Request latest measurements and predictions.
            $data = $this->request()->mapWithKeys(callback: function (array $stationData, int $stationId) use ($onlyInSeason) {
                // Get station from station ID.
                $station = Station::fromId(id: $stationId);

                // Parse measurements for each station.
                $measurements = Collection::make(items: $stationData['data'] ?? [])->mapWithKeys(callback: function (array $allergenData, int $allergenId) use ($station, $stationData) {
                    // Get Allergen from ID.
                    $allergen = Allergen::tryFrom(value: $allergenId);

                    // If the Allergen is not found or the season is not active,
                    // we're going to set the value to null and move on.
                    if ($allergen === null || $allergenData['inSeason'] === false) {
                        return [$allergen?->code() ?? $allergenId => null];
                    }

                    return [$allergen->code() => Collection::make(items: [
                        'date' => $stationData['date'],
                        'value' => (int) $allergenData['level'],
                        'level' => match (true) {
                            $allergenData['level'] <= 0 => Level::Unknown,
                            $allergenData['level'] < $allergen->levels()['low'] => Level::Low,
                            $allergenData['level'] < $allergen->levels()['moderate'] => Level::Moderate,
                            $allergenData['level'] < $allergen->levels()['high'] => Level::High,
                            default => Level::VeryHigh,
                        },
                        'predictions' => $allergen->type() === 'spore' ? null : Collection::make(items: $allergenData['overrides'] ?? [])->map(callback: function ($prediction, $index) use ($allergenData) {
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
                    $measurements = $measurements->filter(callback: fn ($measurement) => $measurement !== null);
                }

                return [$station->value => $measurements->sortKeys()];
            });

            // Return measurements for specific station or all stations.
            return $station !== null ? $data->get(key: $station->value, default: Collection::make()) : $data;
        } catch (Throwable) {
            return Collection::make();
        }
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

                return Collection::make(items: $this->decodeFirestoreFields(fields: $data['fields'] ?? []));
            } catch (Throwable $e) {
                throw new ParsingFailedException(message: 'Failed to parse response body: ' . $e->getMessage(), previous: $e);
            }
        } catch (GuzzleException $e) {
            throw new RequestFailedException(message: 'Failed to request pollen data: ' . $e->getMessage(), previous: $e);
        }
    }

    /**
     * Decode Firestore fields.
     *
     * @param array $fields
     * @return array
     * @throws Exception
     */
    private function decodeFirestoreFields(array $fields): array
    {
        return array_map(callback: fn ($value) => $this->decodeFirestoreValue(value: $value), array: $fields);
    }

    /**
     * Decode Firestore value.
     *
     * @param array $value
     * @return mixed
     * @throws Exception
     */
    private function decodeFirestoreValue(array $value): mixed
    {
        return match (true) {
            array_key_exists(key: 'stringValue', array: $value) => $value['stringValue'],
            array_key_exists(key: 'integerValue', array: $value) => $value['integerValue'],
            array_key_exists(key: 'doubleValue', array: $value) => $value['doubleValue'],
            array_key_exists(key: 'booleanValue', array: $value) => $value['booleanValue'],
            array_key_exists(key: 'nullValue', array: $value) => $value['nullValue'],
            array_key_exists(key: 'timestampValue', array: $value) => $value['timestampValue'],
            array_key_exists(key: 'arrayValue', array: $value) => array_map(callback: fn ($item) => $this->decodeFirestoreValue(value: $item), array: $value['arrayValue']['values'] ?? []),
            array_key_exists(key: 'mapValue', array: $value) => $this->decodeFirestoreFields(fields: $value['mapValue']['fields'] ?? []),
            default => throw new Exception(message: 'Unknown value type', code: 500),
        };
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
    public function getClient():? GuzzleClientInterface
    {
        return $this->client;
    }
}
