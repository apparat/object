<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat_<Package>
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

namespace Apparat\Object\Domain\Model\Object;

/**
 * Object URL
 *
 * @package Apparat\Object\Domain\Model
 */
class Url
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
	 * URL parts
	 *
	 * @var array
	 */
	protected $_urlParts = null;

	/**
	 * Date PCRE pattern
	 *
	 * @var array
	 */
	const DATE_PATTERN = [
		'Y' => '(?P<year>\d{4})/',
		'm' => '(?P<month>\d{2})/',
		'd' => '(?P<day>\d{2})/',
		'H' => '(?P<hour>\d{2})/',
		'i' => '(?P<minute>\d{2})/',
		's' => '(?P<second>\d{2})/',
	];

	/**
	 * Object URL constructor
	 *
	 * @param string $url Object URL
	 */
	public function __construct($url)
	{

		// Parse the URL
		$this->_urlParts = @parse_url($url);
		if ($this->_urlParts === false) {
			throw new InvalidArgumentException(sprintf('Invalid object URL "%s"', $url),
				InvalidArgumentException::INVALID_OBJECT_URL);
		}

//		print_r($this->_urlParts);
		// /2015/10/01/36704.event/36704-1.md


		$datePrecision = getenv('OBJECT_DATE_PRECISION');
		$pathPattern = '%^/'.implode('', array_slice(self::DATE_PATTERN, 0,
				$datePrecision)).'(?P<id>\d+)\.(?P<type>[a-z]+)/\\'.($datePrecision + 1).'(?:-(?P<revision>\d+))?(?P<extension>\.[a-z0-9]+)?$%';
		if (!strlen($this->_urlParts['path']) || !preg_match_all($pathPattern, $this->_urlParts['path'], $pathParts)) {
			throw new InvalidArgumentException(sprintf('Invalid object URL path "%s"', $this->_urlParts['path']),
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
		$this->_revision = new Revision(isset($pathParts['revision']) ? intval($pathParts['revision'][0]) : Revision::CURRENT);

//		print_r($pathParts);
//		echo $pathPattern;
	}

	/**
	 * Return the serialized object URL
	 *
	 * @return string Serialized object URL
	 */
	public function __toString()
	{
		return $this->getUrl();
	}

	/**
	 * Return the full serialized object URL
	 *
	 * @return string Full object URL
	 */
	public function getUrl()
	{
		$url = '';
		return $url;
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
	 * @return Url New object URL
	 */
	public function setCreationDate($creationDate)
	{
		$url = clone $this;
		$url->_creationDate = $creationDate;
		return $url;
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
	 * @return Url New object URL
	 */
	public function setType(Type $type)
	{
		$url = clone $this;
		$url->_type = $type;
		return $url;
	}

	/**
	 * Set the object ID
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Return the object ID
	 *
	 * @param int $id New object ID
	 * @return Url New object URL
	 */
	public function setId($id)
	{
		$url = clone $this;
		$url->_id = $id;
		return $url;
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
	 * @return Url New object URL
	 */
	public function setRevision(Revision $revision)
	{
		$url = clone $this;
		$url->_revision = $revision;
		return $url;
	}
}