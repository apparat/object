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

use Apparat\Object\Application\Model\Object\Article;
use Apparat\Object\Domain\Model\Object\AbstractObject;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Infrastructure\Model\Object\Object;

/**
 * System properties test
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class SystemPropertiesTest extends AbstractRepositoryEnabledTest
{
    /**
     * Example object locator
     *
     * @var string
     */
    const OBJECT_LOCATOR = '/2015/12/21/1-article/1';

    /**
     * Test the instantiation of system properties
     *
     * @expectedException \Apparat\Object\Domain\Model\Object\RuntimeException
     * @expectedExceptionCode 1456520791
     */
    public function testSystemProperties()
    {
        $now = new \DateTimeImmutable('now');
        $data = [
            SystemProperties::PROPERTY_ID => 1,
            SystemProperties::PROPERTY_TYPE => Article::TYPE,
            SystemProperties::PROPERTY_REVISION => 1,
            SystemProperties::PROPERTY_CREATED => $now,
            SystemProperties::PROPERTY_MODIFIED => $now,
            SystemProperties::PROPERTY_LANGUAGE => getenv('OBJECT_DEFAULT_LANGUAGE'),

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

    /**
     * Test setting the object location
     *
     * @expectedException \Apparat\Object\Domain\Model\Properties\InvalidArgumentException
     * @expectedExceptionCode 1462903252
     */
    public function testObjectSetLocation()
    {
        $latitude = rand(0, 10000) / 10000;
        $longitude = rand(0, 10000) / 10000;
        $elevation = rand(0, 10000);
        $article = Object::load(getenv('REPOSITORY_URL').self::OBJECT_LOCATOR);
        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals($latitude, $article->setLatitude($latitude)->getLatitude());

        // Repeat the assignment to test unchanged returns
        $this->assertEquals($latitude, $article->setLatitude($latitude)->getLatitude());

        $this->assertEquals($longitude, $article->setLongitude($longitude)->getLongitude());
        $this->assertEquals($elevation, $article->setElevation($elevation)->getElevation());
        $article->setLatitude('invalid');
    }
}
