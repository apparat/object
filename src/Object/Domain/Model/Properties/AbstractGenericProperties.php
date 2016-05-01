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
        $propertyPath = $this->buildPropertyPath($property);

        // Traverse the property tree
        $mutated = false;
        $propertyPathSteps = [];
        $dataRoot = $this->data;
        $data =& $dataRoot;
        foreach ($propertyPath as $property) {
            $propertyPathSteps[] = $property;

            // If the property name step is invalid
            if (!array_key_exists($property, $data)) {
                $data[$property] = [];
                $mutated = true;
            }

            $data =& $data[$property];
        }

        // If a new property is created with a non-empty value or an existing property is altered: Mutate
        if ($mutated ? !empty($value) : ($value !== $data)) {
            $data = $value;
            return new static($dataRoot, $this->object);
        }

        return $this;
    }
}
