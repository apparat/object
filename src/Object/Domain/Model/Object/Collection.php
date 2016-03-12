<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object\Domain
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
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

use Apparat\Object\Domain\Model\Path\RepositoryPath;

/**
 * Lazy loading object collection
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Collection implements CollectionInterface
{
    /**
     * Objects
     *
     * @var ObjectInterface[]|RepositoryPath[]
     */
    protected $objects = array();
    /**
     * Object IDs
     *
     * @var array
     */
    protected $objectIds = array();
    /**
     * Internal object pointer
     *
     * @var int
     */
    protected $pointer = 0;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Collection constructor
     *
     * @param array $objects Collection objects
     * @throws InvalidArgumentException If the an invalid object or path is provided
     */
    public function __construct(array $objects = [])
    {
        foreach ($objects as $object) {
            // If it's an object
            if ($object instanceof ObjectInterface) {
                $this->objects[$object->getId()->getId()] = $object;
                continue;

                // Else if it's an object path
            } elseif ($object instanceof RepositoryPath) {
                $this->objects[$object->getId()->getId()] = $object;
                continue;
            }

            throw new InvalidArgumentException(
                'Invalid collection object or path',
                InvalidArgumentException::INVALID_COLLECTION_OBJECT_OR_PATH
            );
        }

        $this->objectIds = array_keys($this->objects);
    }

    /**
     * Return the current object
     *
     * @return ObjectInterface Current object
     */
    public function current()
    {
        return $this->loadObject($this->objectIds[$this->pointer]);
    }

    /**
     * Load and return an object by ID
     *
     * @param int $objectId Object ID
     * @return ObjectInterface Object
     */
    protected function loadObject($objectId)
    {
        // Lazy-load the object once
        if ($this->objects[$objectId] instanceof RepositoryPath) {
            $this->objects[$objectId] = $this->objects[$objectId]->getRepository()->loadObject(
                $this->objects[$objectId]
            );
        }

        return $this->objects[$objectId];
    }

    /**
     * Move forward to next object
     *
     * @return void
     */
    public function next()
    {
        ++$this->pointer;
    }

    /**
     * Return the ID of the current object
     *
     * @return int Object ID
     */
    public function key()
    {
        return $this->objectIds[$this->pointer];
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The current position is valid
     */
    public function valid()
    {
        return isset($this->objectIds[$this->pointer]);
    }

    /**
     * Rewind the Iterator to the first object
     *
     * @return void
     */
    public function rewind()
    {
        $this->pointer = 0;
    }

    /**
     * Whether an object ID exists
     *
     * @param int $offset Object ID
     * @return boolean Whether the object ID exists
     */
    public function offsetExists($offset)
    {
        return isset($this->objects[$offset]);
    }

    /**
     * Get an object with a particular ID
     *
     * @param int $offset Object ID
     * @return ObjectInterface Object
     */
    public function offsetGet($offset)
    {
        return $this->objects[$offset];
    }

    /**
     * Set an object by ID
     *
     * @param int $offset Object ID
     * @param ObjectInterface $value Object
     * @throws RuntimeException When an object should be set by ID
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException(sprintf('Cannot modify collection by index (%s / %s). Use add() / remove() instead', $offset, gettype($value)), RuntimeException::CANNOT_MODIFY_COLLECTION_BY_INDEX);
    }

    /**
     * Unset an object by ID
     *
     * @param int $offset Object ID
     * @throws RuntimeException When an object should be set by ID
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException(sprintf('Cannot modify collection by index (%s). Use add() / remove() instead', $offset), RuntimeException::CANNOT_MODIFY_COLLECTION_BY_INDEX);
    }

    /**
     * Add an object to the collection
     *
     * @param string|ObjectInterface $object Object or object URL
     * @return Collection Modified object collection
     */
    public function add($object)
    {
        $objects = $this->objects;
        $objects[] = $object;
        return new self(array_values($objects));
    }

    /**
     * Remove an object out of this collection
     *
     * @param string|ObjectInterface $object Object or object ID
     * @return Collection Modified object collection
     */
    public function remove($object)
    {
        $object = ($object instanceof ObjectInterface) ? $object->getId()->getId() : intval($object);
        if (empty($this->objects[$object])) {
            throw new InvalidArgumentException(
                sprintf('Unknown object ID "%s"', $object),
                InvalidArgumentException::UNKNOWN_OBJECT_ID
            );
        }

        $objects = $this->objects;
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
        return count($this->objects);
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Append another collection
     *
     * @param Collection $collection Collection
     * @return Collection Combined collections
     */
    public function append(Collection $collection)
    {
        $objects = array_merge($this->objects, $collection->objects);
        return new self(array_values($objects));
    }
}
