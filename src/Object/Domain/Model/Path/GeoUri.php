<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
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

namespace Apparat\Object\Domain\Model\Path;

/**
 * Geo URI
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class GeoUri extends Uri
{
    /**
     * Latitude
     *
     * @var float
     */
    protected $latitude;
    /**
     * Longitude
     *
     * @var float
     */
    protected $longitude;
    /**
     * Altitude
     *
     * @var float
     */
    protected $altitude;
    /**
     * Geo schema
     *
     * @var string
     */
    const SCHEME_GEO = 'geo';
    /**
     * Regular expression for matching the Geo coordinates
     *
     * @var string
     */
    const GEO_REGEX = '(?P<latitude>-?\d(?:\.\d+)*),(?P<longitude>-?\d(?:\.\d+)*)(?:,(?P<altitude>-?\d(?:\.\d+)*))?';

    /**
     * Constructor
     *
     * @param string $uri Geo URI
     * @throws InvalidArgumentException If the Geo URI doesn't suffice RFC 5870
     */
    public function __construct($uri)
    {
        parent::__construct($uri);

        // If the Geo URI doesn't suffice RFC 5870
        if (!preg_match('%^'.self::SCHEME_GEO.':'.self::GEO_REGEX.'$%', $this->uri, $geoParts)) {
            throw new InvalidArgumentException(
                sprintf('Invalid RFC 5870 GEO URL "%s"', $this->uri),
                InvalidArgumentException::INVALID_GEO_URL
            );
        }

        $this->latitude = floatval($geoParts['latitude']);
        $this->longitude = floatval($geoParts['longitude']);
        $this->altitude = empty($geoParts['altitude']) ? null : floatval($geoParts['altitude']);
    }

    /**
     * Return the latitude
     *
     * @return float latitude
     */
    public function getLatitude()
    {
        return $this->latitude;
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
     * Return the altitude
     *
     * @return float Altitude
     */
    public function getAltitude()
    {
        return $this->altitude;
    }

    /**
     * Serialize the Geo URI
     *
     * @return string
     */
    public function __toString()
    {
        $geoUri = self::SCHEME_GEO.':'.$this->latitude.','.$this->longitude;
        if ($this->altitude !== null) {
            $geoUri .= ','.$this->altitude;
        }
        return $geoUri;
    }
}