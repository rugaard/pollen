<?php
declare(strict_types=1);

namespace Rugaard\Pollen\Support;

use Tightenco\Collect\Support\Collection;

/**
 * Trait MeasurementTypes.
 *
 * @package Rugaard\Pollen\Support
 */
trait MeasurementTypes
{
    /**
     * Get measurement types.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    protected function getMeasurementTypes() : Collection
    {
        return Collection::make([
            Collection::make([
                'id' => 1,
                'name' => 'El',
                'type' => 'pollen',
            ]), Collection::make([
                'id' => 2,
                'name' => 'Hassel',
                'type' => 'pollen'
            ]), Collection::make([
                'id' => 4,
                'name' => 'Elm',
                'type' => 'pollen'
            ]), Collection::make([
                'id' => 7,
                'name' => 'Birk',
                'type' => 'pollen'
            ]), Collection::make([
                'id' => 28,
                'name' => 'Græs',
                'type' => 'pollen'
            ]), Collection::make([
                'id' => 31,
                'name' => 'Bynke',
                'type' => 'pollen'
            ]), Collection::make([
                'id' => 44,
                'name' => 'Alternaria',
                'type' => 'spore'
            ]), Collection::make([
                'id' => 45,
                'name' => 'Cladosporium',
                'type' => 'spore'
            ])
        ]);
    }

    /**
     * Get measurement type by ID.
     *
     * @param  int $measurementId
     * @return \Tightenco\Collect\Support\Collection
     */
    protected function getMeasurementTypeById(int $measurementId) : Collection
    {
        return $this->getMeasurementTypes()->where('id', '=', $measurementId)->first();
    }

    /**
     * Get measurement level by value.
     *
     * @param  int $measurementId
     * @param  int $value
     * @return string|null
     */
    protected function getMeasurementLevel(int $measurementId, int $value) :? string
    {
        switch ($measurementId) {
            case 1:
            case 4:
            case 28:
            case 31:
                return $this->determineMeasurementLevel($value, 50, 10);
                break;
            case 2:
                return $this->determineMeasurementLevel($value, 15, 5);
                break;
            case 7:
                return $this->determineMeasurementLevel($value, 100, 30);
            case 44:
                return $this->determineMeasurementLevel($value, 100, 20);
            case 45:
                return $this->determineMeasurementLevel($value, 6000, 2000);
            default:
                return null;
        }
    }

    /**
     * Determine measurement level by value.
     *
     * @param  int $value
     * @param  int $highValue
     * @param  int $moderateValue
     * @return string|null
     */
    protected function determineMeasurementLevel(int $value, int $highValue, int $moderateValue) :? string
    {
        if ($value <= 0) {
            return null;
        }

        if ($value > $highValue) {
            return 'high';
        }

        if ($value <= $highValue && $value >= $moderateValue) {
            return 'moderate';
        }

        return 'low';
    }
}