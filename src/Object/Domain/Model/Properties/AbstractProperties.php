<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
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

namespace Apparat\Object\Domain\Model\Properties;

use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Abstract object properties collection
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
abstract class AbstractProperties implements PropertiesInterface
{
    /**
     * Property data
     *
     * @var array
     */
    protected $data = [];
    /**
     * Owner object
     *
     * @var ObjectInterface
     */
    protected $object = null;

    /**
     * Meta properties constructor
     *
     * @param array $data Property data
     * @param ObjectInterface $object Owner object
     */
    public function __construct(array $data, ObjectInterface $object)
    {
        $this->data = $data;
        $this->object = $object;
    }

    /**
     * Return the owner object
     *
     * @return ObjectInterface Owner object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Normalize and sort a property value list
     *
     * @param array $values Property values
     * @return array Normalized and sorted property values
     */
    protected function normalizeSortedPropertyValues(array $values)
    {
        $values = array_unique($values);
        sort($values, SORT_NATURAL);
        return $values;
    }

    /**
     * Mutate a string property
     *
     * @param string $property Property name
     * @param string $value New value
     * @return $this|AbstractProperties Self reference or clone
     */
    protected function mutateStringProperty($property, $value)
    {

        // If the new value differs from the current: Return clone
        if (strval($this->$property) !== strval($value)) {
            $collection = clone $this;
            $collection->$property = strval($value);
            return $collection;
        }

        // Else: return self reference
        return $this;
    }

    /**
     * Mutate a list property
     *
     * @param string $property Property name
     * @param array $values New values
     * @return $this|AbstractProperties Self reference or clone
     */
    protected function mutateListProperty($property, array $values)
    {
        // If the new values differ from the current ones: Return clone
        if (array_diff($this->$property, $values) || array_diff($values, $this->$property)) {
            $collection = clone $this;
            $collection->$property = $values;
            return $collection;
        }

        // Else: return self reference
        return $this;
    }
}
