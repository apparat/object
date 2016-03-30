<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Test
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

use Apparat\Object\Domain\Model\Object\AbstractObject;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\SystemProperties;

/**
 * Property tests
 *
 * @package Apparat\Kernel
 * @subpackage Apparat\Object\Tests
 */
class PropertyTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Test the instantiation of system properties
     *
     * @expectedException \Apparat\Object\Domain\Model\Object\RuntimeException
     * @expectedExceptionCode 1456520791
     */
    public function testSystemProperties()
    {
        $data = [
            'id' => 1,
            'type' => 'article',
            'revision' => 1,
            'created' => time(),
            'hash' => sha1(rand()),
        ];
        /** @var ObjectInterface $object */
        $object = $this->getMockBuilder(AbstractObject::class)->disableOriginalConstructor()->getMock();
        $systemProperties = new SystemProperties($data, $object);
        $this->assertInstanceOf(SystemProperties::class, $systemProperties);
        $systemProperties = $systemProperties->publish();
        $systemProperties->publish();
    }

    /**
     * Test the instantiation of invalid system properties
     *
     * @expectedException \Apparat\Object\Domain\Model\Properties\InvalidArgumentException
     * @expectedExceptionCode 1456522289
     */
    public function testInvalidSystemProperties()
    {
        /** @var ObjectInterface $object */
        $object = $this->getMockBuilder(AbstractObject::class)->disableOriginalConstructor()->getMock();
        new SystemProperties([], $object);
    }
}
