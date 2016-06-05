<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application\Model\Properties\Domain\Traits
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

namespace Apparat\Object\Application\Model\Properties\Domain\Traits;

use Apparat\Object\Application\Model\Properties\Datatype\ApparatUrl;
use Apparat\Object\Application\Model\Properties\Datatype\Geo;
use Apparat\Object\Application\Model\Properties\Datatype\Number;
use Apparat\Object\Application\Model\Properties\Datatype\Sentence;
use Apparat\Object\Application\Model\Properties\Datatype\Text;
use Apparat\Object\Domain\Contract\ObjectTypesInterface;

/**
 * Address properties model trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
trait AddressPropertiesModelTrait
{
    /**
     * Property model: Street address
     *
     * @var array
     */
    protected $pmStreetAddress = [
        false,
        [ApparatUrl::class, Text::class],
        [ApparatUrl::class => [ObjectTypesInterface::ADDRESS]]
    ];
    /**
     * Property model: Extended address
     *
     * @var array
     */
    protected $pmExtendedAddress = [false, [Text::class]];
    /**
     * Property model: Post office box
     *
     * @var array
     */
    protected $pmPostOfficeBox = [false, [Sentence::class]];
    /**
     * Property model: Locality
     *
     * @var array
     */
    protected $pmLocality = [false, [Sentence::class]];
    /**
     * Property model: Region
     *
     * @var array
     */
    protected $pmRegion = [false, [Sentence::class]];
    /**
     * Property model: Postal code
     *
     * @var array
     */
    protected $pmPostalCode = [false, [Sentence::class]];
    /**
     * Property model: Country name
     *
     * @var array
     */
    protected $pmCountryName = [false, [Sentence::class]];
    /**
     * Property model: Label
     *
     * @var array
     */
    protected $pmLabel = [false, [Text::class]];
    /**
     * Property model: Geo
     *
     * @var array
     */
    protected $pmGeo = [
        false,
        [Geo::class],
        [Geo::class => [ObjectTypesInterface::GEO]]
    ];
    /**
     * Property model: Latitude
     *
     * @var array
     */
    protected $pmLatitude = [false, [Number::class]];
    /**
     * Property model: Longitude
     *
     * @var array
     */
    protected $pmLongitude = [false, [Number::class]];
    /**
     * Property model: Altitude
     *
     * @var array
     */
    protected $pmAltitude = [false, [Number::class]];
}