<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framwork
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

use Apparat\Object\Application\Utility\ArrayUtility;
use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Repository\InvalidArgumentException;

/**
 * Selector tests
 *
 * @package Apparat\Object
 * @subpackage ApparatTest
 */
class AuxiliaryText extends AbstractTest
{
	/**
	 * Test an invalid ID
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449876361
	 */
	public function testInvalidId()
	{
		new Id(0);
	}

	/**
	 * Test an invalid type
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449871242
	 */
	public function testInvalidType()
	{
		new Type('invalid');
	}

	/**
	 * Test an invalid Revision
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449871715
	 */
	public function testInvalidRevision()
	{
		new Revision('abc');
	}

	/**
	 * Test a repository invalid argument exception
	 */
	public function testInvalidArgumentException() {
		$exception = new InvalidArgumentException('Test', 0, null, 'test');
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertEquals('test', $exception->getArgumentName());
	}

	/**
	 * Test recursive array sorting by key
	 */
	public function testSortArrayByKeyRecursively() {
		$unsorted = ['b' => 2, 'a' => ['c' => 3, 'a' => 1]];
		$sorted = ['a' => ['a' => 1, 'c' => 3], 'b' => 2];
		$this->assertEquals(serialize($sorted), serialize(ArrayUtility::sortRecursiveByKey($unsorted)));
	}
}