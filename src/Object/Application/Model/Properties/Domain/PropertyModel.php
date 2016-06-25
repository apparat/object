<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Apparat\Object\Domain\Model\Properties
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

namespace Apparat\Object\Application\Model\Properties\Domain;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Application\Model\Properties\Datatype\DatatypeInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Property model
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class PropertyModel
{
    /**
     * Multi-value property
     *
     * @var boolean
     */
    protected $multivalue;
    /**
     * Allowed datatypes
     *
     * @var array
     */
    protected $datatypes;
    /**
     * Datatype filter
     *
     * @var array
     */
    protected $filter;
    /**
     * Owning object
     *
     * @var ObjectInterface
     */
    private $object;

    /**
     * Constructor
     *
     * @param ObjectInterface $object Owning object
     * @param bool $multivalue Multi-value property
     * @param array $datatypes Allowed datatypes
     * @param array $filter Datatype filters
     * @throws InvalidArgumentException If no datatypes are allowed
     * @throws InvalidArgumentException If the datatype is invalid
     */
    public function __construct(ObjectInterface $object, $multivalue, array $datatypes, array $filter = [])
    {
        $this->object = $object;
        $this->multivalue = boolval($multivalue);
        $this->datatypes = $datatypes;
        $this->filter = $filter;

        // If no datatypes are allowed
        if (!count($this->datatypes)) {
            throw new InvalidArgumentException(
                'Invalid property datatypes list',
                InvalidArgumentException::INVALID_PROPERTY_DATATYPES
            );
        }

        // Validate & instantiate the datatypes
        foreach ($this->datatypes as $datatypeIndex => $datatype) {
            // If the datatype is invalid
            if (!$datatype
                || !class_exists($datatype)
                || !(new \ReflectionClass($datatype))->implementsInterface(DatatypeInterface::class)
            ) {
                throw new InvalidArgumentException(
                    sprintf('Invalid property datatype "%s"', $datatype),
                    InvalidArgumentException::INVALID_PROPERTY_DATATYPE
                );
            }

            $this->datatypes[$datatypeIndex] = Kernel::create(
                $datatype,
                [$this->object, empty($this->filter[$datatype]) ? [] : (array)$this->filter[$datatype]]
            );
        }
    }

    /**
     * Filter a property value
     *
     * @param mixed $value Property value
     * @return mixed Filtered value
     * @throws DomainException If the domain property value is invalid
     */
    public function filterValue($value)
    {
        // If the property model expects an array as value
        if ($this->multivalue) {
            $values = (array)$value;
            foreach ($values as $index => $value) {
                $values[$index] = $this->filterSingleValue($value);
            }
            return $values;
        }

        return $this->filterSingleValue($value);
    }

    /**
     * Filter a single property value
     *
     * @param mixed $value Property value
     * @return mixed Filtered value
     * @throws DomainException If the domain property value is invalid
     */
    protected function filterSingleValue($value)
    {
        // If the value is empty
        if (empty($value)) {
            return null;
        }

        // Run through all allowed datatypes
        /** @var DatatypeInterface $datatype */
        foreach ($this->datatypes as $datatype) {
            try {
                return $datatype->match($value);
            } catch (DomainException $e) {
                continue;
            } catch (\Exception $e) {
                break;
            }
        }

        // If the domain property value is invalid
        throw new DomainException(
            sprintf('Invalid domain property value "%s"', $value),
            DomainException::INVALID_DOMAIN_PROPERTY_VALUE
        );
    }

    /**
     * Return whether this property model is multivalued
     *
     * @return boolean Multivalued
     */
    public function isMultivalue()
    {
        return $this->multivalue;
    }
}
