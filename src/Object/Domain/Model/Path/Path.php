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

namespace Apparat\Object\Domain\Model\Path;

use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * Object path
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Path implements PathInterface
{
	/**
	 * Creation date
	 *
	 * @var \DateTimeImmutable
	 */
	protected $_creationDate = null;
	/**
	 * Object ID
	 *
	 * @var int
	 */
	protected $_id = null;
	/**
	 * Object type
	 *
	 * @var Type
	 */
	protected $_type = null;
	/**
	 * Object revision
	 *
	 * @var Revision
	 */
	protected $_revision = null;
	/**
	 * Date PCRE pattern
	 *
	 * @var array
	 * @see Selector::$_datePattern
	 */
	protected static $_datePattern = [
		'Y' => '(?P<year>\d{4})/',
		'm' => '(?P<month>\d{2})/',
		'd' => '(?P<day>\d{2})/',
		'H' => '(?P<hour>\d{2})/',
		'i' => '(?P<minute>\d{2})/',
		's' => '(?P<second>\d{2})/',
	];

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Object URL constructor
	 *
	 * @param string $path Object path
	 */
	public function __construct($path)
	{
		$datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));
		$pathPattern = '%^/'.implode('', array_slice(self::$_datePattern, 0,
				$datePrecision)).'(?P<id>\d+)\.(?P<type>[a-z]+)(?:/(.*\.)?\\k<id>(?:-(?P<revision>\d+))?(?P<extension>\.[a-z0-9]+)?)?$%';
		if (empty($path) || !preg_match_all($pathPattern, $path, $pathParts)) {
			throw new InvalidArgumentException(sprintf('Invalid object URL path "%s"',
				empty($path) ? '(empty)' : $path),
				InvalidArgumentException::INVALID_OBJECT_URL_PATH);
		}
		if ($datePrecision) {
			$year = $pathParts['year'][0];
			$month = isset($pathParts['month']) ? $pathParts['month'][0] : '01';
			$day = isset($pathParts['day']) ? $pathParts['day'][0] : '01';
			$hour = isset($pathParts['hour']) ? $pathParts['hour'][0] : '00';
			$minute = isset($pathParts['minute']) ? $pathParts['minute'][0] : '00';
			$second = isset($pathParts['second']) ? $pathParts['second'][0] : '00';
			$this->_creationDate = new \DateTimeImmutable("${year}-${month}-${day}T${hour}:${minute}:${second}+00:00");
		}

		// Set the ID
		$this->_id = new Id(intval($pathParts['id'][0]));

		// Set the type
		$this->_type = new Type($pathParts['type'][0]);

		// Set the revision
		$this->_revision = new Revision(empty($pathParts['revision'][0]) ? Revision::CURRENT : intval($pathParts['revision'][0]));
	}

	/**
	 * Create and return the object URL path
	 *
	 * @return string Object path
	 */
	public function __toString()
	{
		$path = [];
		$datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));

		// Add the creation date
		foreach (array_slice(array_keys(self::$_datePattern), 0, $datePrecision) as $dateFormat) {
			$path[] = $this->_creationDate->format($dateFormat);
		}

		// Add the object ID and type
		$path[] = $this->_id->getId().'.'.$this->_type->getType();

		// Add the ID and revision
		$path[] = rtrim($this->_id->getId().'-'.$this->_revision->getRevision(), '-');

		return '/'.implode('/', $path);
	}

	/**
	 * Return the object's creation date
	 *
	 * @return \DateTimeImmutable Object creation date
	 */
	public function getCreationDate()
	{
		return $this->_creationDate;
	}

	/**
	 * Set the object's creation date
	 *
	 * @param \DateTimeImmutable $creationDate
	 * @return Path New object path
	 */
	public function setCreationDate(\DateTimeImmutable $creationDate)
	{
		$path = clone $this;
		$path->_creationDate = $creationDate;
		return $path;
	}

	/**
	 * Return the object type
	 *
	 * @return Type Object type
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Set the object type
	 *
	 * @param Type $type Object type
	 * @return Path New object path
	 */
	public function setType(Type $type)
	{
		$path = clone $this;
		$path->_type = $type;
		return $path;
	}

	/**
	 * Return the object ID
	 *
	 * @return Id Object ID
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Set the object ID
	 *
	 * @param Id $id Object ID
	 * @return Path New object path
	 */
	public function setId(Id $id)
	{
		$path = clone $this;
		$path->_id = $id;
		return $path;
	}

	/**
	 * Return the object revision
	 *
	 * @return Revision Object revision
	 */
	public function getRevision()
	{
		return $this->_revision;
	}

	/**
	 * Set the object revision
	 *
	 * @param Revision $revision Object revision
	 * @return Path New object path
	 */
	public function setRevision(Revision $revision)
	{
		$path = clone $this;
		$path->_revision = $revision;
		return $path;
	}
}