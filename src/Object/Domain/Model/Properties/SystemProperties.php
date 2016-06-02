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

use Apparat\Kernel\Tests\Kernel;
use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\RuntimeException;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * Object system properties collection
 *
 * In general, the system properties are used as read-only collection, with one exception: Draft objects don't have the
 * `published` property set, so there's a {@link publish()} method for advancing an object's state.
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class SystemProperties extends AbstractProperties
{
    /**
     * Collection name
     *
     * @var string
     */
    const COLLECTION = 'system';
    /**
     * ID property
     *
     * @var string
     */
    const PROPERTY_ID = 'id';
    /**
     * Type property
     *
     * @var string
     */
    const PROPERTY_TYPE = 'type';
    /**
     * Revision property
     *
     * @var string
     */
    const PROPERTY_REVISION = 'revision';
    /**
     * Created property
     *
     * @var string
     */
    const PROPERTY_CREATED = 'created';
    /**
     * Modified property
     *
     * @var string
     */
    const PROPERTY_MODIFIED = 'modified';
    /**
     * Published property
     *
     * @var string
     */
    const PROPERTY_PUBLISHED = 'published';
    /**
     * Deleted property
     *
     * @var string
     */
    const PROPERTY_DELETED = 'deleted';
    /**
     * Language property
     *
     * @var string
     */
    const PROPERTY_LANGUAGE = 'language';
    /**
     * Location property
     *
     * @var string
     */
    const PROPERTY_LOCATION = 'location';
    /**
     * Object ID (constant throughout revisions)
     *
     * @var Id
     */
    protected $uid = null;
    /**
     * Object type (constant throughout revisions)
     *
     * @var Type
     */
    protected $type = null;
    /**
     * Object revision
     *
     * @var Revision
     */
    protected $revision = null;
    /**
     * Creation date of this revision
     *
     * @var \DateTimeImmutable
     */
    protected $created = null;
    /**
     * Modification date of this revision
     *
     * @var \DateTimeImmutable
     */
    protected $modified = null;
    /**
     * Publication date of this revision
     *
     * @var \DateTimeImmutable
     */
    protected $published = null;
    /**
     * Deletion date of this revision
     *
     * @var \DateTimeImmutable
     */
    protected $deleted = null;
    /**
     * Location
     *
     * @var LocationProperties
     */
    protected $location = null;
    /**
     * Language (BCP 47 compliant)
     *
     * @var string
     * @see https://tools.ietf.org/html/bcp47
     */
    protected $language = null;

    /**
     * System properties constructor
     *
     * @param array $data Property data
     * @param ObjectInterface $object Owner object
     */
    public function __construct(array $data, ObjectInterface $object)
    {
        parent::__construct($data, $object);

        // Initialize the object ID
        if (array_key_exists(self::PROPERTY_ID, $data)) {
            $this->uid = Id::unserialize($data[self::PROPERTY_ID]);
        }

        // Initialize the object type
        if (array_key_exists(self::PROPERTY_TYPE, $data)) {
            $this->type = Type::unserialize($data[self::PROPERTY_TYPE]);
        }

        // Initialize the object creation date
        if (array_key_exists(self::PROPERTY_CREATED, $data)) {
            $this->created =  $data[self::PROPERTY_CREATED];
        }

        // Initialize the object modification date
        if (array_key_exists(self::PROPERTY_MODIFIED, $data)) {
            $this->modified =$data[self::PROPERTY_MODIFIED];
        }

        // Initialize the object publication date
        if (array_key_exists(self::PROPERTY_PUBLISHED, $data)) {
            $this->published = $data[self::PROPERTY_PUBLISHED];
        }

        // Initialize the object deletion date
        if (array_key_exists(self::PROPERTY_DELETED, $data)) {
            $this->deleted = $data[self::PROPERTY_DELETED];
        }

        // Initialize the object language
        if (array_key_exists(self::PROPERTY_LANGUAGE, $data)) {
            $this->language = trim($data[self::PROPERTY_LANGUAGE]);
        }

        // Initialize the location
        $this->location = Kernel::create(
            LocationProperties::class,
            [empty($data[self::PROPERTY_LOCATION]) ? [] : $data[self::PROPERTY_LOCATION], $this->object]
        );

        // Initialize the object revision
        if (array_key_exists(self::PROPERTY_REVISION, $data)) {
            $this->revision = Revision::unserialize($data[self::PROPERTY_REVISION])->setDraft($this->isDraft());
        }

        // Test if all mandatory properties are set
        if (!($this->uid instanceof Id)
            || !($this->type instanceof Type)
            || !($this->revision instanceof Revision)
            || !($this->created instanceof \DateTimeInterface)
            || !($this->modified instanceof \DateTimeInterface)
            || !strlen($this->language)
        ) {
            throw new InvalidArgumentException(
                'Invalid system properties',
                InvalidArgumentException::INVALID_SYSTEM_PROPERTIES
            );
        }
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->uid;
    }

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Return the object draft mode
     *
     * @return boolean Object draft mode
     */
    public function isDraft()
    {
        return !($this->published instanceof \DateTimeInterface);
    }

    /**
     * Return the object publication state
     *
     * @return boolean Object is published
     */
    public function isPublished()
    {
        return ($this->published instanceof \DateTimeInterface);
    }

    /**
     * Return the object deletion state
     *
     * @return boolean Object is deleted
     */
    public function isDeleted()
    {
        return ($this->deleted instanceof \DateTimeInterface);
    }

    /**
     * Return the creation date & time of this revision
     *
     * @return \DateTimeImmutable Creation date & time
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Return the modification date & time of this revision
     *
     * @return \DateTimeImmutable Modification date & time
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Return the publication date & time of this revision
     *
     * @return \DateTimeImmutable|null Publication date & time
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Return the deletion date & time of this revision
     *
     * @return \DateTimeImmutable|null Deletion date & time
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Return the object language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Return the latitude
     *
     * @return float Latitude
     */
    public function getLatitude()
    {
        return $this->location->getLatitude();
    }

    /**
     * Set the latitude
     *
     * @param float $latitude Latitude
     * @return SystemProperties Self reference
     */
    public function setLatitude($latitude)
    {
        return $this->mutatePropertiesProperty(
            self::PROPERTY_LOCATION,
            $this->location->setLatitude($latitude)
        );
    }

    /**
     * Return the longitude
     *
     * @return float Longitude
     */
    public function getLongitude()
    {
        return $this->location->getLongitude();
    }

    /**
     * Set the longitude
     *
     * @param float $longitude Longitude
     * @return SystemProperties Self reference
     */
    public function setLongitude($longitude)
    {
        return $this->mutatePropertiesProperty(
            self::PROPERTY_LOCATION,
            $this->location->setLongitude($longitude)
        );
    }

    /**
     * Return the elevation
     *
     * @return float Elevation
     */
    public function getElevation()
    {
        return $this->location->getElevation();
    }

    /**
     * Set the elevation
     *
     * @param float $elevation
     * @return SystemProperties Self reference
     */
    public function setElevation($elevation)
    {
        return $this->mutatePropertiesProperty(
            self::PROPERTY_LOCATION,
            $this->location->setElevation($elevation)
        );
    }

    /**
     * Derive draft system properties
     *
     * @param Revision $draftRevision Draft revision
     * @return SystemProperties Draft system properties
     */
    public function createDraft(Revision $draftRevision)
    {
        $now = new \DateTimeImmutable('now');
        return new static(
            [
                self::PROPERTY_ID => $this->uid->getId(),
                self::PROPERTY_TYPE => $this->type->getType(),
                self::PROPERTY_REVISION => $draftRevision->getRevision(),
                self::PROPERTY_CREATED => $now,
                self::PROPERTY_MODIFIED => $now,
                self::PROPERTY_LANGUAGE => $this->language,
            ],
            $this->object
        );
    }

    /**
     * Indicate that the object got published
     *
     * @return SystemProperties System properties
     * @throws RuntimeException If the object is already published
     */
    public function publish()
    {
        // If the object is already published
        if ($this->published instanceof \DateTimeInterface) {
            throw new RuntimeException(
                'Cannot republish object previously published at '.$this->published->format('c'),
                RuntimeException::CANNOT_REPUBLISH_OBJECT
            );
        }

        $systemProperties = clone $this;
        $systemProperties->published = new \DateTimeImmutable();
        $systemProperties->revision = $this->revision->setDraft(false);
        return $systemProperties;
    }

    /**
     * Update the object's modification timestamp
     *
     * @return SystemProperties System properties
     */
    public function touch()
    {
        $systemProperties = clone $this;
        $systemProperties->modified = new \DateTimeImmutable();
        return $systemProperties;
    }

    /**
     * Set the object's deletion timestamp
     *
     * @return SystemProperties System properties
     */
    public function delete()
    {
        $systemProperties = clone $this;
        $systemProperties->deleted = new \DateTimeImmutable();
        return $systemProperties;
    }

    /**
     * Unset the object's deletion timestamp
     *
     * @return SystemProperties System properties
     */
    public function undelete()
    {
        $systemProperties = clone $this;
        $systemProperties->deleted = null;
        return $systemProperties;
    }

    /**
     * Return the property values as array
     *
     * @return array Property values
     */
    public function toArray()
    {
        return array_filter([
            self::PROPERTY_ID => $this->uid->getId(),
            self::PROPERTY_TYPE => $this->type->getType(),
            self::PROPERTY_REVISION => $this->revision->getRevision(),
            self::PROPERTY_CREATED => $this->created,
            self::PROPERTY_MODIFIED => $this->modified,
            self::PROPERTY_PUBLISHED => $this->published,
            self::PROPERTY_DELETED => $this->deleted,
            self::PROPERTY_LOCATION => $this->location->toArray(),
            self::PROPERTY_LANGUAGE => $this->language,
        ]);
    }
}
