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

use Apparat\Object\Application\Utility\ArrayUtility;
use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Abstract generic object properties collection
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
abstract class AbstractGenericProperties extends AbstractProperties implements GenericPropertiesInterface
{
    /**
     * Property collection constructor
     *
     * @param array $data Property data
     * @param ObjectInterface $object Owner object
     */
    public function __construct(array $data, ObjectInterface $object)
    {
        parent::__construct($data, $object);
    }

    /**
     * Return the property values as array
     *
     * @return array Property values
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Get a property value
     *
     * Multi-level properties might be traversed by property name paths separated with colons (":").
     *
     * @param string $property Property name
     * @return mixed Property value
     * @throws InvalidArgumentException If the property name is invalid
     */
    public function getProperty($property)
    {
        $propertyPath = $this->buildPropertyPath($property);

        // Traverse the property tree
        $propertyPathSteps = [];
        $data =& $this->data;
        foreach ($propertyPath as $property) {
            $propertyPathSteps[] = $property;

            // If the property name step is invalid
            if (!array_key_exists($property, $data)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid property name "%s"',
                        implode(self::PROPERTY_TRAVERSAL_SEPARATOR, $propertyPathSteps)
                    ),
                    InvalidArgumentException::INVALID_PROPERTY_NAME
                );
            }

            $data =& $data[$property];
        }

        return $data;
    }

    /**
     * Translate a property name to a property path segments
     *
     * @param string $property Property name
     * @return array Property path
     * @throws InvalidArgumentException If the property name is empty
     */
    protected function buildPropertyPath($property)
    {
        $propertyPath = array_filter(array_map('trim', explode(self::PROPERTY_TRAVERSAL_SEPARATOR, $property)));

        // If the property traversal path is empty
        if (!count($propertyPath)) {
            throw new InvalidArgumentException('Empty property name', InvalidArgumentException::EMPTY_PROPERTY_NAME);
        }

        return $propertyPath;
    }

    /**
     * Set a property value
     *
     * Multi-level properties might be traversed by property name paths separated with colons (":").
     *
     * @param string $property Property name
     * @param mixed $value Property value
     * @return GenericPropertiesInterface Self reference
     */
    public function setProperty($property, $value)
    {
        return $this->setPropertyPath($this->buildPropertyPath($property), $this->data, $value);
    }

    /**
     * Set a property value by path list and base data
     *
     * @param array $propertyPath Path list
     * @param array $propertyTree Base data
     * @param mixed $value Property value
     * @return GenericPropertiesInterface Self reference
     */
    protected function setPropertyPath(array $propertyPath, array $propertyTree, $value)
    {
        // Traverse the property tree and find the property node to set
        $created = false;
        $data =& $this->findPropertyNode($propertyPath, $propertyTree, $created);

        // If a new property is created with a non-empty value or an existing property is altered: Mutate
        if ($created ? !empty($value) : !$this->assertEquals($data, $value)) {
            $data = $value;
            return new static($propertyTree, $this->object);
        }

        return $this;
    }

    /**
     * Traverse the property tree and return a possibly node
     *
     * @param array $propertyPath Property name path
     * @param array $propertyTree Copy of the current property tree
     * @param boolean $created Property has been created
     * @return mixed Property node
     */
    protected function &findPropertyNode(
        array $propertyPath,
        array &$propertyTree,
        &$created
    ) {
        $data =& $propertyTree;

        // Run through all sub-properties
        foreach ($propertyPath as $property) {
            // Create the sub-property if it doesn't exist
            if (!array_key_exists($property, $data)) {
                $data[$property] = [];
                $created = true;
            }

            $data =& $data[$property];
        }

        return $data;
    }

    /**
     * Assert that two values equal
     *
     * @param mixed $expected Expected value
     * @param mixed $actual Actual value
     * @return boolean Actual value equals the expected one
     */
    protected function assertEquals($expected, $actual)
    {
        // If both values don't have the same type
        if (gettype($expected) !== gettype($actual)) {
            return false;
        }

        // If we are comparing arrays
        if (is_array($expected)) {
            return ArrayUtility::reduce($expected) == ArrayUtility::reduce($actual);
        }

        // Compare the values
        return $expected == $actual;
    }
}
