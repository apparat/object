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

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Application\Model\Properties\Datatype\ApparatUrl;
use Apparat\Object\Application\Model\Properties\Domain\PropertyModel;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Ports\Object;

/**
 * Property model tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class PropertyModelTest extends AbstractRepositoryEnabledTest
{
    /**
     * Object
     *
     * @var ObjectInterface
     */
    protected static $object;
    /**
     * Example object path
     *
     * @var string
     */
    const ARTICLE_PATH = '/2015/12/21/1-article/1';
    /**
     * Example Url
     *
     * @var string
     */
    const URL = 'http://example.com/path/to/resource?var=val';

    /**
     * Setup
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$object = Object::instance(getenv('REPOSITORY_URL').self::ARTICLE_PATH);
    }

    /**
     * Test a property model with empty datatypes
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\InvalidArgumentException
     * @expectedExceptionCode 1465140832
     */
    public function testWithEmptyDatatypes()
    {
        Kernel::create(PropertyModel::class, [self::$object, false, []]);
    }

    /**
     * Test a property model with an invalid datatype
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\InvalidArgumentException
     * @expectedExceptionCode 1465141877
     */
    public function testWithInvalidDatatype()
    {
        Kernel::create(PropertyModel::class, [self::$object, false, [PropertyModel::class]]);
    }

    /**
     * Test filtering with a datatype exception
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     * @expectedExceptionCode 1465140806
     */
    public function testFilteringWithDatatypeException()
    {
        /** @var PropertyModel $propertyModel */
        $propertyModel = Kernel::create(
            PropertyModel::class,
            [self::$object, false, [ApparatUrl::class], [ApparatUrl::class => Object::CONTACT]]
        );
        $this->assertNull($propertyModel->filterValue(''));
        $propertyModel->filterValue(self::ARTICLE_PATH);
    }
}
