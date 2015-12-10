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
 * Lazy loading object collection
 *
 * @package Apparat\Object\Domain\Model\Object
 */
class ObjectCollection implements \Countable, \Iterator
{
	/**
	 * Objects
	 *
	 * @var Object[]
	 */
	protected $_objects = array();
	/**
	 * Object IDs
	 *
	 * @var array
	 */
	protected $_objectIds = array();
	/**
	 * Internal object pointer
	 *
	 * @var int
	 */
	protected $_pointer = 0;

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Collection constructor
	 *
	 * @param array $objects Collection objects
	 */
	public function __construct(array $objects)
	{
		foreach ($objects as $object) {
			if ($object instanceof Object) {
				$this->_objects[$object->getId()] = $object;
			} else {
				if (!($object instanceof ObjectUrl)) {
					$object = new ObjectUrl(strval($object));
				}
				$this->_objects[$object->getId()] = $object->getUrl();
			}
		}

		$this->_objectIds = array_keys($this->_objects);
	}

	/**
	 * Return the current object
	 *
	 * @return Object Current object
	 */
	public function current()
	{
		return $this->_loadObject($this->_objectIds[$this->_pointer]);
	}

	/**
	 * Move forward to next object
	 *
	 * @return void
	 */
	public function next()
	{
		++$this->_pointer;
	}

	/**
	 * Return the ID of the current object
	 *
	 * @return int Object ID
	 */
	public function key()
	{
		return $this->_objectIds[$this->_pointer];
	}

	/**
	 * Checks if current position is valid
	 *
	 * @return boolean The current position is valid
	 */
	public function valid()
	{
		return isset($this->_objectIds[$this->_pointer]);
	}

	/**
	 * Rewind the Iterator to the first object
	 *
	 * @return void
	 */
	public function rewind()
	{
		$this->_pointer = 0;
	}

	/**
	 * Whether an object ID exists
	 *
	 * @param int $offset Object ID
	 * @return boolean Whether the object ID exists
	 */
	public function offsetExists($offset)
	{
		return isset($this->_objects[$offset]);
	}

	/**
	 * Get an object with a particular ID
	 *
	 * @param int $offset Object ID
	 * @return Object Object
	 */
	public function offsetGet($offset)
	{
		return $this->_objects[$offset];
	}

	/**
	 * Set an object by ID
	 *
	 * @param int $offset Object ID
	 * @param Object $value Object
	 * @return void
	 * @throws RuntimeException When an object should be set by ID
	 */
	public function offsetSet($offset, $value)
	{

	}

	/**
	 * Unset an object by ID
	 *
	 * @param int $offset Object ID
	 * @return ObjectCollection Object collection with the object removed
	 */
	public function offsetUnset($offset)
	{
		$objects = $this->_objects;
		unset($objects[$offset]);
		return new self($objects);
	}

	/**
	 * Add an object to the collection
	 *
	 * @param string|Object $object Object or object URL
	 * @return ObjectCollection Modified object collection
	 */
	public function addObject($object)
	{

		// If the object is not yet an object instance
		if (!($object instanceof Object)) {
			$object = new ObjectUrl(strval($object));
			$object = $object->getUrl();
		}

		$objects = $this->_objects;
		$objects[] = $object;
		return new self(array_values($objects));
	}

	/**
	 * Remove an object out of this collection
	 *
	 * @param string|Object $object Object or object ID
	 * @return ObjectCollection Modified object collection
	 */
	public function removeObject($object)
	{
		if ($object instanceof Object) {
			$object = $object->getId();
		} else {
			$object = intval($object);
		}
		if (empty($this->_objects[$object])) {
			throw new InvalidArgumentException(sprintf('Unknown object ID "%s"', $object),
				InvalidArgumentException::UNKNOWN_OBJECT_ID);
		}

		$objects = $this->_objects;
		unset($objects[$object]);
		return new self(array_values($objects));
	}

	/**
	 * Count objects in this collection
	 *
	 * @return int The number of objects in this collection
	 */
	public function count()
	{
		return count($this->_objects);
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Load and return an object by ID
	 *
	 * @param int $objectId Object ID
	 * @return Object Object
	 */
	protected function _loadObject($objectId)
	{
		return $this->_objects[$objectId];
	}
}