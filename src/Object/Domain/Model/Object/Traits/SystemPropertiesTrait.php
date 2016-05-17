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

use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Properties\SystemProperties;

/**
 * System properties trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 * @property array $collectionStates
 */
trait SystemPropertiesTrait
{
    /**
     * System properties
     *
     * @var SystemProperties
     */
    protected $systemProperties;

    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision()
    {
        return $this->systemProperties->getRevision();
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->systemProperties->getId();
    }

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getType()
    {
        return $this->systemProperties->getType();
    }

    /**
     * Return the creation date & time
     *
     * @return \DateTimeImmutable Creation date & time
     */
    public function getCreated()
    {
        return $this->systemProperties->getCreated();
    }

    /**
     * Return the modification date & time
     *
     * @return \DateTimeImmutable Modification date & time
     */
    public function getModified()
    {
        return $this->systemProperties->getModified();
    }

    /**
     * Return the publication date & time
     *
     * @return \DateTimeImmutable|null Publication date & time
     */
    public function getPublished()
    {
        return $this->systemProperties->getPublished();
    }

    /**
     * Return the deletion date & time
     *
     * @return \DateTimeImmutable Deletion date & time
     */
    public function getDeleted()
    {
        return $this->systemProperties->getDeleted();
    }

    /**
     * Return the language
     *
     * @return string Language
     */
    public function getLanguage()
    {
        return $this->systemProperties->getLanguage();
    }

    /**
     * Return the latitude
     *
     * @return float Latitude
     */
    public function getLatitude()
    {
        return $this->systemProperties->getLatitude();
    }

    /**
     * Set the latitude
     *
     * @param float $latitude Latitude
     * @return SystemProperties Self reference
     */
    public function setLatitude($latitude)
    {
        $this->setSystemProperties($this->systemProperties->setLatitude($latitude));
        return $this;
    }

    /**
     * Set the system properties collection
     *
     * @param SystemProperties $systemProperties System property collection
     * @param bool $overwrite Overwrite the existing collection (if present)
     */
    protected function setSystemProperties(SystemProperties $systemProperties, $overwrite = false)
    {
        $this->systemProperties = $systemProperties;
        $systemPropsState = spl_object_hash($this->systemProperties);

        // If the system property collection state has changed
        if (!$overwrite
            && !empty($this->collectionStates[SystemProperties::COLLECTION])
            && ($systemPropsState !== $this->collectionStates[SystemProperties::COLLECTION])
        ) {
            // Flag this object as mutated
            $this->setDirtyState();
        }

        $this->collectionStates[SystemProperties::COLLECTION] = $systemPropsState;
    }

    /**
     * Return the longitude
     *
     * @return float Longitude
     */
    public function getLongitude()
    {
        return $this->systemProperties->getLongitude();
    }

    /**
     * Set the longitude
     *
     * @param float $longitude Longitude
     * @return SystemProperties Self reference
     */
    public function setLongitude($longitude)
    {
        $this->setSystemProperties($this->systemProperties->setLongitude($longitude));
        return $this;
    }

    /**
     * Return the elevation
     *
     * @return float Elevation
     */
    public function getElevation()
    {
        return $this->systemProperties->getElevation();
    }

    /**
     * Set the elevation
     *
     * @param float $elevation
     * @return SystemProperties Self reference
     */
    public function setElevation($elevation)
    {
        $this->setSystemProperties($this->systemProperties->setElevation($elevation));
        return $this;
    }

    /**
     * Set the object state to dirty
     */
    abstract protected function setDirtyState();
}
