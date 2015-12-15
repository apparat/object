<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Repository;

use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * Repository selector
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Selector implements SelectorInterface
{
	/**
	 * Year component
	 *
	 * @var int
	 */
	private $_year = null;
	/**
	 * Month component
	 *
	 * @var int
	 */
	private $_month = null;
	/**
	 * Day component
	 *
	 * @var int
	 */
	private $_day = null;
	/**
	 * Hour component
	 *
	 * @var int
	 */
	private $_hour = null;
	/**
	 * Minute component
	 *
	 * @var int
	 */
	private $_minute = null;
	/**
	 * Second component
	 *
	 * @var int
	 */
	private $_second = null;
	/**
	 * Object ID
	 *
	 * @var int
	 */
	private $_id = null;
	/**
	 * Object type
	 *
	 * @var string
	 */
	private $_type = null;
	/**
	 * Revision component
	 *
	 * @var int
	 */
	private $_revision = null;
	/**
	 * Wildcard
	 *
	 * @var string
	 */
	const WILDCARD = '*';

	/**
	 * Repository selector constructor
	 *
	 * @param string|int|NULL $year
	 * @param string|int|NULL $month
	 * @param string|int|NULL $day
	 * @param string|int|NULL $hour
	 * @param string|int|NULL $minute
	 * @param string|int|NULL $second
	 * @param string|int|NULL $id Object ID
	 * @param string|NULL $type Object type
	 * @param int|NULL $revision
	 * @throws InvalidArgumentException If any of the components isn't valid
	 */
	public function __construct(
		$year = self::WILDCARD,
		$month = self::WILDCARD,
		$day = self::WILDCARD,
		$hour = self::WILDCARD,
		$minute = self::WILDCARD,
		$second = self::WILDCARD,
		$id = self::WILDCARD,
		$type = self::WILDCARD,
		$revision = Revision::CURRENT
	) {
		$datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));

		// Validate the creation date and ID components
		foreach (array_slice([
			'year' => $year,
			'month' => $month,
			'day' => $day,
			'hour' => $hour,
			'minute' => $minute,
			'second' => $second
		], 0, $datePrecision) as $label => $component) {

			// If the component isn't valid
			if (!is_int($component) && ($component !== self::WILDCARD)) {
				throw new InvalidArgumentException(sprintf('Invalid repository selector '.$label.' component "%s"',
					$component), InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT, null, $label);
			}

			// Set the component value
			$this->{"_$label"} = $component;
		}

		// If the ID component isn't valid
		if (!is_int($id) && ($id !== self::WILDCARD)) {
			throw new InvalidArgumentException(sprintf('Invalid repository selector ID component "%s"',
				$id), InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT, null, 'id');
		}
		$this->_id = $id;

		// If the type component isn't valid
		if (!Type::isValidType($type) && ($type !== self::WILDCARD)) {
			throw new InvalidArgumentException(sprintf('Invalid repository selector type component "%s"',
				$type), InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT, null, 'type');
		}
		$this->_type = $type;

		// If the revision component isn't valid
		if (!Revision::isValidRevision($revision) && ($revision !== self::WILDCARD)) {
			throw new InvalidArgumentException(sprintf('Invalid repository selector revision component "%s"',
				$revision), InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT, null, 'revision');
		}
		$this->_revision = $revision;
	}

	/**
	 * Return the year component
	 *
	 * @return int Year component
	 */
	public function getYear()
	{
		return $this->_year;
	}

	/**
	 * Return the month component
	 *
	 * @return int Month component
	 */
	public function getMonth()
	{
		return $this->_month;
	}

	/**
	 * Return the day component
	 *
	 * @return int Day component
	 */
	public function getDay()
	{
		return $this->_day;
	}

	/**
	 * Return the hour component
	 *
	 * @return int Hour component
	 */
	public function getHour()
	{
		return $this->_hour;
	}

	/**
	 * Return the minute component
	 *
	 * @return int
	 */
	public function getMinute()
	{
		return $this->_minute;
	}

	/**
	 * Return the second component
	 *
	 * @return int
	 */
	public function getSecond()
	{
		return $this->_second;
	}

	/**
	 * Return the ID component
	 *
	 * @return int ID component
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Return the Type component
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Return the revision component
	 *
	 * @return int Revision component
	 */
	public function getRevision()
	{
		return $this->_revision;
	}
}