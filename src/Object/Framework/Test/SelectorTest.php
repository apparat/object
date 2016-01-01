<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framwork
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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

namespace ApparatTest;

use Apparat\Object\Domain\Factory\SelectorFactory;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Repository\InvalidArgumentException;
use Apparat\Object\Domain\Repository\Selector as RepositorySelector;

/**
 * Selector tests
 *
 * @package Apparat\Object
 * @subpackage ApparatTest
 */
class SelectorTest extends AbstractTest
{
	/**
	 * Example selector
	 *
	 * @var string
	 */
	const SELECTOR = '/2015/10/01/36704.event/36704-1';

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
	}

	/**
	 * Test a valid full-fledged selector with wildcards
	 */
	public function testFactoryValidSelectorWildcards()
	{
		$datePrecision = getenv('OBJECT_DATE_PRECISION');
		putenv('OBJECT_DATE_PRECISION=6');
		$selector = SelectorFactory::createFromString('/*/*/*/*/*/*/*.*/*');
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getYear());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getMonth());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getDay());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getHour());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getMinute());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getSecond());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getId());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getType());
		$this->assertEquals(Revision::CURRENT, $selector->getRevision());
		putenv('OBJECT_DATE_PRECISION='.$datePrecision);
	}

	/**
	 * Test minimal selector
	 */
	public function testFactoryMinimalSelector()
	{
		$selector = SelectorFactory::createFromString('/*');
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getYear());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getMonth());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getDay());
		$this->assertEquals(null, $selector->getHour());
		$this->assertEquals(null, $selector->getMinute());
		$this->assertEquals(null, $selector->getSecond());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getId());
		$this->assertEquals(RepositorySelector::WILDCARD, $selector->getType());
		$this->assertEquals(Revision::CURRENT, $selector->getRevision());
	}

	/**
	 * Test an invalid selector
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449961609
	 */
	public function testFactoryInvaldiSelector()
	{
		SelectorFactory::createFromString('invalid');
	}

	/**
	 * Test an invalid date component
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449999646
	 * @expectedExceptionMessageRegExp %year%
	 */
	public function testInvalidDateComponent()
	{
		new RepositorySelector('invalid');
	}

	/**
	 * Test an invalid ID component
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449999646
	 * @expectedExceptionMessageRegExp %id%
	 */
	public function testInvalidIdComponent()
	{
		new RepositorySelector(2015, 1, 1, null, null, null, 'invalid');
	}

	/**
	 * Test an invalid type component
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449999646
	 * @expectedExceptionMessageRegExp %type%
	 */
	public function testInvalidTypeComponent()
	{
		new RepositorySelector(2015, 1, 1, null, null, null, 1, 'invalid');
	}

	/**
	 * Test an invalid revision component
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449999646
	 * @expectedExceptionMessageRegExp %revision%
	 */
	public function testInvalidRevisionComponent()
	{
		new RepositorySelector(2015, 1, 1, null, null, null, 1, 'event', 'invalid');
	}
}