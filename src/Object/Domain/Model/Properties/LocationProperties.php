<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Object
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy of
 *  this software and associated documentation files (the "Software"), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace Apparat\Object\Domain\Model\Properties;

use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Object location
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class LocationProperties extends AbstractProperties
{
    /**
     * Latitude
     *
     * @var string
     */
    const LATITUDE = 'latitude';
    /**
     * Latitude
     *
     * @var string
     */
    const LONGITUDE = 'longitude';
    /**
     * Elevation
     *
     * @var string
     */
    const ELEVATION = 'elevation';
    /**
     * Latitude
     *
     * @var float
     */
    protected $latitude = null;
    /**
     * Longitude
     *
     * @var float
     */
    protected $longitude = null;
    /**
     * Elevation
     *
     * @var float
     */
    protected $elevation = null;

    /**
     * Location constructor
     *
     * @param array $data Property data
     * @param ObjectInterface $object Owner object
     */
    public function __construct(array $data, ObjectInterface $object)
    {
        parent::__construct($data, $object);

        // Set the latitude
        if (!empty($data[self::LATITUDE]) && $this->validateLocationProperty($data[self::LATITUDE])) {
            $this->latitude = $data[self::LATITUDE];
        }

        // Set the longitude
        if (!empty($data[self::LONGITUDE]) && $this->validateLocationProperty($data[self::LONGITUDE])) {
            $this->longitude = $data[self::LONGITUDE];
        }

        // Set the elevation
        if (!empty($data[self::ELEVATION]) && $this->validateLocationProperty($data[self::ELEVATION])) {
            $this->elevation = $data[self::ELEVATION];
        }
    }

    /**
     * Validate a location value
     *
     * @param float|null $value Location property value
     * @return boolean Location value validity
     * @throws InvalidArgumentException If the location property value is not a float
     */
    protected function validateLocationProperty($value)
    {
        // If the location property value is not a float
        if (($value !== null) && !is_numeric($value)) {
            throw new InvalidArgumentException(
                sprintf('Invalid property location value "%s"', $value),
                InvalidArgumentException::INVALID_LOCATION_PROPERTY_VALUE
            );
        }

        return true;
    }

    /**
     * Return the latitude
     *
     * @return float Latitude
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set the latitude
     *
     * @param float $latitude Latitude
     * @return LocationProperties Self reference
     */
    public function setLatitude($latitude)
    {
        $this->validateLocationProperty($latitude);
        return $this->mutateFloatProperty(self::LATITUDE, $latitude);
    }

    /**
     * Return the longitude
     *
     * @return float Longitude
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set the longitude
     *
     * @param float $longitude Longitude
     * @return LocationProperties Self reference
     */
    public function setLongitude($longitude)
    {
        $this->validateLocationProperty($longitude);
        return $this->mutateFloatProperty(self::LONGITUDE, $longitude);
    }

    /**
     * Return the elevation
     *
     * @return float Elevation
     */
    public function getElevation()
    {
        return $this->elevation;
    }

    /**
     * Set the elevation
     *
     * @param float $elevation
     * @return LocationProperties Self reference
     */
    public function setElevation($elevation)
    {
        $this->validateLocationProperty($elevation);
        return $this->mutateFloatProperty(self::ELEVATION, $elevation);
    }

    /**
     * Return the location values as array
     *
     * @return array LocationProperties values
     */
    public function toArray()
    {
        return array_filter([
            self::LATITUDE => $this->latitude,
            self::LONGITUDE => $this->longitude,
            self::ELEVATION => $this->elevation,
        ]);
    }
}
