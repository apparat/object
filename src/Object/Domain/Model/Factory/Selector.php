<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\<Layer>
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

namespace Apparat\Object\Domain\Model\Factory;

use Apparat\Object\Domain\Model\Repository\InvalidArgumentException;

/**
 * Object selector factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Selector
{
	/**
	 * Date PCRE pattern
	 *
	 * @var array
	 * @see Url::$_datePattern
	 */
	protected static $_datePattern = [
		'Y' => '(?P<year>(?:\d{4})|\*)/',
		'm' => '(?P<month>(?:\d{2})}|\*)/',
		'd' => '(?P<day>(?:\d{2})}|\*)/',
		'H' => '(?P<hour>(?:\d{2})}|\*)/',
		'i' => '(?P<minute>(?:\d{2})}|\*)/',
		's' => '(?P<second>(?:\d{2})}|\*)/',
	];

	/**
	 * Parse and instantiate an object selector
	 *
	 * @param string $selector String selector
	 * @return \Apparat\Object\Domain\Model\Repository\Selector Object selector
	 * @throws InvalidArgumentException If the selector is invalid
	 */
	public static function parse($selector)
	{
		$datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));
		$selectorPattern = '%^/'.implode('', array_slice(self::$_datePattern, 0,
				$datePrecision)).'(?P<id>(?:\d+)|\*)\.(?P<type>(?:[a-z]+)|\*)$%';

		// If the selector is invalid
		if (!preg_match($selectorPattern, $selector, $selectorParts)) {
			throw new InvalidArgumentException(sprintf('Invalid respository selector "%s"', $selector),
				InvalidArgumentException::INVALID_REPOSITORY_SELECTOR);
		}

		if ($datePrecision) {
			$year = $selectorParts['year'][0];
			$month = isset($selectorParts['month']) ? $selectorParts['month'][0] : '01';
			$day = isset($selectorParts['day']) ? $selectorParts['day'][0] : '01';
			$hour = isset($selectorParts['hour']) ? $selectorParts['hour'][0] : '00';
			$minute = isset($selectorParts['minute']) ? $selectorParts['minute'][0] : '00';
			$second = isset($selectorParts['second']) ? $selectorParts['second'][0] : '00';
			$creationDate = new \DateTimeImmutable("${year}-${month}-${day}T${hour}:${minute}:${second}+00:00");
		} else {
			$creationDate = null;
		}

		// TODO: Wildcard date, ID & type
		return new \Apparat\Object\Domain\Model\Repository\Selector($creationDate, 0, '');
	}
}