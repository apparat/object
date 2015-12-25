<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
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

namespace Apparat\Object\Application\Model\Properties;

use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * System object properties collection
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class SystemProperties extends AbstractProperties
{
	/**
	 * Object ID
	 *
	 * @var Id
	 */
	protected $_id = 0;

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
	 * Publication date
	 *
	 * @var \DateTimeImmutable
	 */
	protected $_published = null;

	/**
	 * Object hash
	 *
	 * @var string
	 */
	protected $_hash = null;

	/**
	 * Collection name
	 *
	 * @var string
	 */
	const COLLECTION = 'system';

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * System properties constructor
	 *
	 * @param array $data System properties
	 */
	public function __construct(array $data)
	{
		parent::__construct($data);

		// Initialize the object ID
		if (array_key_exists('id', $this->_data)) {
			$this->_setId(new Id(intval($this->_data['id'])));
		}

		// Initialize the object type
		if (array_key_exists('type', $this->_data)) {
			$this->_setType(new Type($this->_data['type']));
		}

		// Initialize the object revision
		if (array_key_exists('revision', $this->_data)) {
			$this->_setRevision(new Revision(intval($this->_data['revision'])));
		}

		// Initialize the object publication date
		if (array_key_exists('published', $this->_data)) {
			$this->_setPublished(new \DateTimeImmutable('@'.$this->_data['published']));
		}

		// Initialize the object hash
		if (array_key_exists('hash', $this->_data)) {
			$this->_setHash($this->_data['hash']);
		}
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
	 * Return the object type
	 *
	 * @return Type Object type
	 */
	public function getType()
	{
		return $this->_type;
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
	 * Return the publication date & time
	 *
	 * @return \DateTimeImmutable Publication date & time
	 */
	public function getPublished()
	{
		return $this->_published;
	}

	/**
	 * Return the object hash
	 *
	 * @return string Object hash
	 */
	public function getHash()
	{
		return $this->_hash;
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Set the object ID
	 *
	 * @param Id $id
	 */
	protected function _setId(Id $id)
	{
		$this->_id = $id;
	}

	/**
	 * Set the object type
	 *
	 * @param Type $type Object type
	 */
	protected function _setType(Type $type)
	{
		$this->_type = $type;
	}

	/**
	 * Set the object revision
	 *
	 * @param Revision $revision Object revision
	 */
	protected function _setRevision(Revision $revision)
	{
		$this->_revision = $revision;
	}

	/**
	 * Set the publication date & time
	 *
	 * @param \DateTimeImmutable $published Publication date & time
	 */
	protected function _setPublished(\DateTimeImmutable $published)
	{
		$this->_published = $published;
	}

	/**
	 * Set the object hash
	 *
	 * @param string $hash Object hash
	 */
	protected function _setHash($hash)
	{
		$this->_hash = $hash;
	}
}