<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framwork
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
use Apparat\Object\Domain\Factory\SelectorFactory;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Repository\Selector as RepositorySelector;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Object\Ports\Object;

/**
 * Selector tests
 *
 * @package Apparat\Object
 * @subpackage ApparatTest
 */
class SelectorTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Example selector
     *
     * @var string
     */
    const SELECTOR = '/2015/10/01/36704-event/36704-1';
    /**
     * Example selector with hidden object
     *
     * @var string
     */
    const HIDDEN_SELECTOR = '/2015/10/01/.36704-event/36704-1';
    /**
     * Example selector with optionally hidden object
     *
     * @var string
     */
    const OPTIONAL_HIDDEN_SELECTOR = '/2015/10/01/{.,}36704-event/36704-1';

    /**
     * Test a valid full-fledged selector
     */
    public function testFactoryValidSelector()
    {
        $selector = SelectorFactory::createFromString(self::SELECTOR);
        $this->assertInstanceOf(RepositorySelector::class, $selector);
        $this->assertEquals(2015, $selector->getYear());
        $this->assertEquals(10, $selector->getMonth());
        $this->assertEquals(1, $selector->getDay());
        $this->assertEquals(null, $selector->getHour());
        $this->assertEquals(null, $selector->getMinute());
        $this->assertEquals(null, $selector->getSecond());
        $this->assertEquals(36704, $selector->getId());
        $this->assertEquals('event', $selector->getType());
        $this->assertEquals(1, $selector->getRevision());
        $this->assertEquals(SelectorInterface::VISIBLE, $selector->getVisibility());
    }

    /**
     * Test a valid selector with hidden object
     */
    public function testFactoryHiddenSelector()
    {
        $selector = SelectorFactory::createFromString(self::HIDDEN_SELECTOR);
        $this->assertEquals(SelectorInterface::HIDDEN, $selector->getVisibility());
    }

    /**
     * Test a valid selector with optionally hidden object
     */
    public function testFactoryOptionallyHiddenSelector()
    {
        $selector = SelectorFactory::createFromString(self::OPTIONAL_HIDDEN_SELECTOR);
        $this->assertEquals(SelectorInterface::ALL, $selector->getVisibility());
    }

    /**
     * Test a valid full-fledged selector with wildcards
     */
    public function testFactoryValidSelectorWildcards()
    {
        $datePrecision = getenv('OBJECT_DATE_PRECISION');
        putenv('OBJECT_DATE_PRECISION=6');
        $selector = SelectorFactory::createFromString('/*/*/*/*/*/*/*-*/*');
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getYear());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getMonth());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getDay());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getHour());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getMinute());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getSecond());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getId());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getType());
        $this->assertEquals(Revision::CURRENT, $selector->getRevision());
        putenv('OBJECT_DATE_PRECISION='.$datePrecision);
    }

    /**
     * Test minimal selector
     */
    public function testFactoryMinimalSelector()
    {
        $selector = SelectorFactory::createFromString('/*');
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getYear());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getMonth());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getDay());
        $this->assertEquals(null, $selector->getHour());
        $this->assertEquals(null, $selector->getMinute());
        $this->assertEquals(null, $selector->getSecond());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getId());
        $this->assertEquals(SelectorInterface::WILDCARD, $selector->getType());
        $this->assertEquals(Revision::CURRENT, $selector->getRevision());
    }

    /**
     * Test an invalid selector
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449961609
     */
    public function testFactoryInvalidSelector()
    {
        SelectorFactory::createFromString('invalid');
    }

    /**
     * Test an invalid date component
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %year%
     */
    public function testInvalidDateComponent()
    {
        Kernel::create(RepositorySelector::class, ['invalid']);
    }

    /**
     * Test an invalid ID component
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %id%
     */
    public function testInvalidIdComponent()
    {
        Kernel::create(RepositorySelector::class, [2015, 1, 1, null, null, null, 'invalid']);
    }

    /**
     * Test an invalid type component
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %type%
     */
    public function testInvalidTypeComponent()
    {
        Kernel::create(RepositorySelector::class, [2015, 1, 1, null, null, null, 1, 'invalid']);
    }

    /**
     * Test an invalid revision component
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %revision%
     */
    public function testInvalidRevisionComponent()
    {
        Kernel::create(RepositorySelector::class, [2015, 1, 1, null, null, null, 1, Object::EVENT, 'invalid']);
    }

    /**
     * Test an invalid object visibility
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %visibility%
     */
    public function testInvalidVisibilityComponent()
    {
        Kernel::create(
            RepositorySelector::class,
            [2015, 1, 1, null, null, null, 1, Object::EVENT, Revision::CURRENT, 0]
        );
    }
}
