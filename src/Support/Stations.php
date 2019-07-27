<?php
declare(strict_types=1);

namespace Rugaard\Pollen\Support;

use Tightenco\Collect\Support\Collection;

/**
 * Trait Stations.
 *
 * @package Rugaard\Pollen\Support
 */
trait Stations
{
    /**
     * Get stations.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    protected function getStations() : Collection
    {
        return Collection::make([
            Collection::make([
                'id' => 48,
                'name' => 'København',
                'code' => 'copenhagen',
                'region' => 'east'
            ]), Collection::make([
                'id' => 49,
                'name' => 'Viborg',
                'code' => 'viborg',
                'region' => 'west'
            ]),
        ]);
    }

    /**
     * Get station by code.
     * 
     * @param  string $stationCode
     * @return \Tightenco\Collect\Support\Collection|null
     */
    protected function getStationByCode(string $stationCode) :? Collection
    {
        return $this->getStations()->where('code', '=', $stationCode)->first();
    }
}