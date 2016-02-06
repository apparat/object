<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Model\Object;

use Apparat\Object\Domain\Contract\SerializablePropertyInterface;

/**
 * Object ID
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Id implements SerializablePropertyInterface
{
	/**
	 * Object ID
	 *
	 * @var int
	 */
	protected $_id = null;

	/**
	 * ID constructor
	 *
	 * @param int $id Object ID
	 */
	public function __construct($id)
	{
		// If the ID is invalid
		if (!is_int($id) || ($id <= 0)) {
			throw new InvalidArgumentException(sprintf('Invalid object ID "%s"', $id),
				InvalidArgumentException::INVALID_OBJECT_ID);
		}

		$this->_id = $id;
	}

	/**
	 * Return the object ID
	 *
	 * @return int Object ID
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * Serialize the property
	 *
	 * @return mixed Property serialization
	 */
	public function serialize()
	{
		return $this->getId();
	}

	/**
	 * Unserialize the string representation of this property
	 *
	 * @param string $str Serialized property
	 * @return Id ID property
	 */
	public static function unserialize($str)
	{
		return new static(intval($str));
	}
}