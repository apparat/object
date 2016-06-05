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

use Apparat\Kernel\Ports\Kernel;

/**
 * Abstract domain object properties collection
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
abstract class AbstractDomainProperties extends AbstractGenericProperties
{
    /**
     * Collection name
     *
     * @var string
     */
    const COLLECTION = 'domain';
    /**
     * Name
     *
     * @var string
     */
    const NAME = 'name';

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
     * Set a property value
     *
     * @param mixed $property Property
     * @param mixed $value Value
     * @param array $propertyPath Property path
     * @param PropertyModel $propertyModel Property model
     */
    protected function setPropertyValue(
        &$property,
        $value,
        array $propertyPath = null,
        PropertyModel $propertyModel = null
    ) {
        // If the property path is not empty
        if (is_array($propertyPath) && count($propertyPath)) {
            // Filter the value if a property model is given
            if ($propertyModel) {
                $value = $propertyModel->filterValue($value);
            }

            // Determine the setter name
            $propertySetter = 'setPm'.implode(array_map('ucfirst', $propertyPath));

            // If there's an explicit setter for this property: Use it
            if (is_callable([$this, $propertySetter])) {
                $this->$propertySetter($property, $value);
                return;
            }

            $property = $value;
        }
    }

    /**
     * Traverse the property tree and return a node
     *
     * @param array $propertyPath Property name path
     * @param array $propertyTree Copy of the current property tree
     * @param boolean $created Property has been created
     * @param array $propertyModel Property model
     * @return mixed Property node
     * @throws DomainException If an invalid subproperty should be allocated
     */
    protected function &findPropertyNode(
        array $propertyPath,
        array &$propertyTree,
        &$created,
        PropertyModel &$propertyModel = null
    ) {
        $propertyModel = null;
        $propertyModelName = 'pm';
        $propertyPathSteps = [];
        $data =& $propertyTree;

        // Run through all sub-properties
        foreach ($propertyPath as $property) {
            // If an invalid sub-property should be allocated
            if ($propertyModel !== null) {
                throw new DomainException(
                    sprintf('Property data model of "%s" doesn\'t allow subproperties',
                        implode(self::PROPERTY_TRAVERSAL_SEPARATOR, $propertyPathSteps)),
                    DomainException::INVALID_DOMAIN_SUBPROPERTY
                );
            }

            $propertyPathSteps[] = $property;
            $propertyModelName .= ucfirst($property);
            /** @var PropertyModel $propertyModel */
            $propertyModel = isset($this->$propertyModelName) ?
                Kernel::create(PropertyModel::class, array_merge([$this->object], $this->$propertyModelName)) : null;

            // If the sub-property doesn't exist
            if (!array_key_exists($property, $data)) {
                $data[$property] = (($propertyModel === null) || $propertyModel->isMultivalue()) ? [] : null;
                $created = true;
            }

            $data =& $data[$property];
        }

        return $data;
    }
}
