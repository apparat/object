<?php

/**
 * apparat-object
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
class ObjectUrl
{
	/**
	 * Object ID
	 *
	 * @var int
	 */
	protected $_id = null;
	/**
	 * Object URL constructor
	 *
	 * @param string $url Object URL
	 */
	public function __construct($url)
	{
	}

	/**
	 * Return the serialized object URL
	 *
	 * @return string Serialized object URL
	 */
	public function __toString() {
		return $this->getUrl();
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
	 * @return ObjectUrl New object URL
	 */
	public function setId($id)
	{
		$url = clone $this;
		$url->_id = $id;
		return $url;
	}

	/**
	 * Return the full serialized object URL
	 *
	 * @return string Full object URL
	 */
	public function getUrl() {
		$url = '';
		return $url;
	}
}