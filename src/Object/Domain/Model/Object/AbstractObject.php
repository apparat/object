<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Object\Domain
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
 * Abstract object implementation
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
abstract class AbstractObject implements ObjectInterface
{
	/**
	 * Object creation date
	 *
	 * @var \DateTimeImmutable
	 */
	private $_creationDate;
	/**
	 * Object ID
	 *
	 * @var Id
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
	 * Abstract object constructor
	 *
	 * @param \DateTimeImmutable $creationDate Object creation date (if already persisted)
	 * @param Id|null $id Object ID (if already persisted)
	 * @param Revision|null $revision Object revision (if already persisted)
	 */
	public function __construct(\DateTimeImmutable $creationDate = null, Id $id = null, Revision $revision = null)
	{
		$this->_id = $id;
		$this->_revision = $revision;
		$this->_creationDate = $creationDate;
	}

	/**
	 * @inheritDoc
	 */
	public function getId()
	{
		return $this->_id;
	}

	/**
	 * @inheritDoc
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * @inheritDoc
	 */
	public function getRevision()
	{
		return $this->_revision;
	}
}