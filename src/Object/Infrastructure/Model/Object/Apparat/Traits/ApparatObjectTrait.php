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
    public function __call($name, array $arguments)
    {
        // If a getter was called
        if (!strncmp('get', $name, 3)) {
            $property = lcfirst(substr($name, 3));
            if (array_key_exists($property, $this->mapping)) {
                $arguments = (array)$this->mapping[$property];
                $getter = 'get'.ucfirst(array_shift($arguments));
                return $this->delegateObjectGetter($property, $getter, $arguments);
            }
        }

        // If the method is unknown
        throw new \BadMethodCallException(sprintf('Unknown apparat object method "%s()"', $name));
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
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * Unset a particular property
     *
     * @param string $offset Property name
     */
    public function offsetUnset($offset)
    {
        throw new InvalidArgumentException(
            sprintf('Cannot unset apparat object property "%s"', $offset),
            InvalidArgumentException::CANNOT_UNSET_APPARAT_OBJECT_PROPERTY
        );
    }

    /**
     * Delegate the mapped object getter
     *
     * @param string $property Property name
     * @param string $getter Getter name
     * @param array $arguments Getter arguments
     * @return mixed Property value
     * @throws InvalidArgumentException If the property is invalid
     */
    protected function delegateObjectGetter($property, $getter, array $arguments)
    {
        // If the property is invalid
        if (!is_callable([$this->object, $getter])) {
            throw new InvalidArgumentException(
                sprintf('Invalid apparat object property "%s"', $property),
                InvalidArgumentException::INVALID_APPARAT_OBJECT_PROPERTY
            );
        }

        return $this->object->$getter(...$arguments);
    }
}
