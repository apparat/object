<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Infrastructure
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
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

namespace Apparat\Object\Infrastructure\Model\Object\Apparat\Traits;

use Apparat\Object\Application\Model\Object\ApplicationObjectInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Infrastructure\Model\Object\Apparat\AbstractApparatObject;
use Apparat\Object\Ports\Exceptions\InvalidArgumentException;

/**
 * Apparat object trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 * @property ApplicationObjectInterface $object
 */
trait ApparatObjectTrait
{
    /**
     * Property mapping
     *
     * @var array
     */
    protected $mapping = [];

    /**
     * Generic getter
     *
     * @param string $method Method name
     * @param array $arguments Arguments
     * @throws \BadMethodCallException If the method is unknown
     */
    public function __call($method, array $arguments)
    {
        // If a getter was called
        if (!strncmp('get', $method, 3)) {
            $property = lcfirst(substr($method, 3));
            if (array_key_exists($property, $this->mapping)) {
                $arguments = (array)$this->mapping[$property];
                $getter = 'get'.ucfirst(array_shift($arguments));
                return $this->delegateObjectGetter($property, $getter, $arguments);
            }
        }

        // If the method is unknown
        throw new \BadMethodCallException(sprintf('Unknown apparat object method "%s()"', $method));
    }

    /**
     * Return whether a particular property exists
     *
     * @param string $offset Property name
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->mapping);
    }

    /**
     * Return a particular property
     *
     * @param string $offset Property name
     * @return mixed Property value
     * @throws InvalidArgumentException If the requested property is invalid
     */
    public function offsetGet($offset)
    {
        // If a known object property has been requested
        if (array_key_exists($offset, $this->mapping)) {
            $arguments = (array)$this->mapping[$offset];
            $property = array_shift($arguments);
            $getter = 'get'.ucfirst($property);
            return $this->delegateObjectGetter($property, $getter, $arguments);
        }

        throw new InvalidArgumentException(
            sprintf('Invalid apparat object property "%s"', $offset),
            InvalidArgumentException::INVALID_APPARAT_OBJECT_PROPERTY
        );
    }

    /**
     * Set a particular property
     *
     * @param string $offset Property name
     * @param mixed $value Property value
     * @throws InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        throw new InvalidArgumentException(
            sprintf('Cannot set apparat object property "%s"', $offset),
            InvalidArgumentException::CANNOT_SET_APPARAT_OBJECT_PROPERTY
        );
    }

    /**
     * Unset a particular property
     *
     * @param string $offset Property name
     * @throws InvalidArgumentException
     */
    public function offsetUnset($offset)
    {
        throw new InvalidArgumentException(
            sprintf('Cannot unset apparat object property "%s"', $offset),
            InvalidArgumentException::CANNOT_UNSET_APPARAT_OBJECT_PROPERTY
        );
    }

    /**
     * Append a value
     *
     * @param mixed $value Value
     * @throws InvalidArgumentException
     */
    public function append($value)
    {
        throw new InvalidArgumentException(
            sprintf('Cannot append apparat object value "%s"', $value),
            InvalidArgumentException::CANNOT_APPEND_APPARAT_OBJECT_VALUE
        );
    }

    /**
     * Return an array copy of all object properties
     *
     * @return array Object properties
     */
    public function getArrayCopy()
    {
        // TODO: Run through the mapping and return all values
        return [];
    }

    /**
     * Return the number of object properties
     *
     * @return int Number of object properties
     */
    public function count()
    {
        return count($this->mapping);
    }

    /**
     * Sort the object properties by value
     *
     * @return void
     */
    public function asort()
    {
        // Do nothing
    }

    /**
     * Sort the entries by key
     * @link http://php.net/manual/en/arrayobject.ksort.php
     * @return void
     * @since 5.2.0
     */
    /**
     * Sort the object properties by key
     *
     * @return void
     */
    public function ksort()
    {
        // Do nothing
    }

    /**
     * Sort the object properties by user function
     *
     * @param \Callable $compareFunction User function
     * @return void
     */
    public function uasort($compareFunction)
    {
        // Do nothing
    }

    /**
     * Sort the object properties by name and user function
     *
     * @param \Callable $compareFunction User function
     * @return void
     */
    public function uksort($cmp_function)
    {
        // Do nothing
    }

    /**
     * Sort the object properties using a "natural order" algorithm
     *
     * @return void
     */
    public function natsort()
    {
        // Do nothing
    }

    /**
     * Sort the object properties using a case insensitive "natural order" algorithm
     *
     * @return void
     */
    public function natcasesort()
    {
        // Do nothing
    }

    /**
     * Unserialize the apparat object
     *
     * @param string $serialized Serialized apparat object
     * @return AbstractApparatObject Unserialized apparat object
     */
    public function unserialize($serialized)
    {
        return unserialize($serialized);
    }

    /**
     * Serialize the apparat object
     *
     * @return AbstractApparatObject Serialized apparat object
     */
    public function serialize()
    {
        // TODO: Serialization should consist of repository + object locators (or object URL?)
        return '';
    }

    /**
     * Exchange the associated object
     *
     * @param mixed $object Object
     * @return ObjectInterface Former object
     */
    public function exchangeArray($object)
    {
        // If a valid option was given
        if ($object instanceof ObjectInterface) {
            $formerObject = $this->object;
            $this->object = $object;
            return $formerObject;
        }

        throw new InvalidArgumentException(
            sprintf('Invalid exchange object'),
            InvalidArgumentException::INVALID_EXCHANGE_OBJECT
        );
    }
}
