<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Model\Object\Traits;

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\AbstractDomainProperties;
use Apparat\Object\Domain\Model\Properties\GenericPropertiesInterface;

/**
 * Domain properties trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 * @property array $collectionStates
 */
trait DomainPropertiesTrait
{
    /**
     * Domain properties
     *
     * @var GenericPropertiesInterface
     */
    protected $domainProperties;
    /**
     * Domain property collection class
     *
     * @var string
     */
    protected $domainPropertyCClass = AbstractDomainProperties::class;

    /**
     * Get a domain property value
     *
     * Multi-level properties might be traversed by property name paths separated with colons (":").
     *
     * @param string $property Property name
     * @return mixed Property value
     */
    public function getDomain($property)
    {
        return $this->domainProperties->getProperty($property);
    }

    /**
     * Set a domain property value
     *
     * @param string $property Property name
     * @param mixed $value Property value
     * @return ObjectInterface Self reference
     */
    public function setDomain($property, $value)
    {
        $this->setDomainProperties($this->domainProperties->setProperty($property, $value));
        return $this;
    }

    /**
     * Set the domain properties collection
     *
     * @param GenericPropertiesInterface $domainProperties Domain property collection
     * @param bool $overwrite Overwrite the existing collection (if present)
     */
    protected function setDomainProperties(GenericPropertiesInterface $domainProperties, $overwrite = false)
    {
        $this->domainProperties = $domainProperties;
        $domainPropsState = spl_object_hash($this->domainProperties);

        // If the domain property collection state has changed
        if (!$overwrite
            && !empty($this->collectionStates[AbstractDomainProperties::COLLECTION])
            && ($domainPropsState !== $this->collectionStates[AbstractDomainProperties::COLLECTION])
        ) {
            // Flag this object as mutated
            $this->setMutatedState();
        }

        $this->collectionStates[AbstractDomainProperties::COLLECTION] = $domainPropsState;
    }

    /**
     * Set the object state to mutated
     */
    abstract protected function setMutatedState();
}
