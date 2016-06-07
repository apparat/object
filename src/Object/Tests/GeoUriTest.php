<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Infrastructure
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

namespace Apparat\Object\Tests;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Path\GeoUri;

/**
 * Geo URI test
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Test
 */
class GeoUriTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Test a valid Geo URI
     */
    public function testGeoUri()
    {
        /** @var GeoUri $geo */
        $geo = Kernel::create(GeoUri::class, ['geo:1.23,-9.87']);
        $this->assertInstanceOf(GeoUri::class, $geo);
        $this->assertEquals(1.23, $geo->getLatitude());
        $this->assertEquals(-9.87, $geo->getLongitude());
        $this->assertNull($geo->getAltitude());
    }

    /**
     * Test an invalid Geo URI
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\InvalidArgumentException
     * @expectedExceptionCode 1465153737
     */
    public function testInvalidGeoUri()
    {
        Kernel::create(GeoUri::class, ['geo:1.23,abc']);
    }

    /**
     * Test Geo URI serialization
     */
    public function testGeoUriSerialization()
    {
        $geo = Kernel::create(GeoUri::class, ['geo:1.23,-9.87,234.5']);
        $this->assertEquals('geo:1.23,-9.87,234.5', strval($geo));
    }
}
