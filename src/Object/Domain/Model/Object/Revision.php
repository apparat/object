<?php

/**
 * apparat-resource
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

namespace Apparat\Object\Domain\Model\Object;

/**
 * Object revision
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain\Model\Object
 */
class Revision
{
	/**
	 * Object revision number
	 *
	 * @var int
	 */
	protected $_revision = null;
	/**
	 * Current revision
	 *
	 * @var null
	 */
	const CURRENT = null;
	/**
	 * Draft revision
	 *
	 * @var string
	 */
	const DRAFT = 0;

	/**
	 * Revision constructor
	 *
	 * @param int $revision Object revision number
	 */
	public function __construct($revision)
	{
		// If the revision number is invalid
		if (!self::isValidRevision($revision)) {
			throw new InvalidArgumentException(sprintf('Invalid object revision number "%s"', $revision),
				InvalidArgumentException::INVALID_OBJECT_REVISION);
		}

		$this->_revision = $revision;
	}

	/**
	 * Return the object revision number
	 *
	 * @return int Object revision number
	 */
	public function getRevision()
	{
		return $this->_revision;
	}

	/**
	 * Test whether a revision number is valid
	 *
	 * @param int|NULL $revision Revision number
	 * @return bool Is valid revision
	 */
	public static function isValidRevision($revision) {
		return ($revision === self::CURRENT) || (is_int($revision) && ($revision >= self::DRAFT));
	}
}