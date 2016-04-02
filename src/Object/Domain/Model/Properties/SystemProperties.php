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
     * Published property
     *
     * @var string
     */
    const PROPERTY_PUBLISHED = 'published';
    /**
     * Hash property
     *
     * @var string
     */
    const PROPERTY_HASH = 'hash';
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
     * Creation date of this revisioon
     *
     * @var \DateTimeImmutable
     */
    protected $created = null;
    /**
     * Publication date of this revision
     *
     * @var \DateTimeImmutable
     */
    protected $published = null;
    /**
     * Object hash of this revision
     *
     * @var string
     */
    protected $hash = '';

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

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

        // Initialize the object revision
        if (array_key_exists(self::PROPERTY_REVISION, $data)) {
            $this->revision = Revision::unserialize($data[self::PROPERTY_REVISION]);
        }

        // Initialize the object creation date
        if (array_key_exists(self::PROPERTY_CREATED, $data)) {
            $this->created = new \DateTimeImmutable('@'.$data[self::PROPERTY_CREATED]);
        }

        // Initialize the object publication date
        if (array_key_exists(self::PROPERTY_PUBLISHED, $data)) {
            $this->published = new \DateTimeImmutable('@'.$data[self::PROPERTY_PUBLISHED]);
        }

        // Initialize the object hash
        if (array_key_exists(self::PROPERTY_HASH, $data)) {
            $this->hash = $data[self::PROPERTY_HASH];
        }

        // Test if all mandatory properties are set
        if (
            !($this->uid instanceof Id) ||
            !($this->type instanceof Type) ||
            !($this->revision instanceof Revision) ||
            !($this->created instanceof \DateTimeImmutable) ||
            !$this->hasValidHash()
        ) {
            throw new InvalidArgumentException(
                'Invalid system properties',
                InvalidArgumentException::INVALID_SYSTEM_PROPERTIES
            );
        }
    }

    /**
     * Test if the object hash is a valid sha1 value
     *
     * @return bool The object hash is a valid sha1 value
     */
    protected function hasValidHash()
    {
        return preg_match('%[a-fA-F0-9]{40}%', $this->hash);
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
     * Return the creation date & time of this revision
     *
     * @return \DateTimeImmutable Creation date & time
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Return the publication date & time of this revision
     *
     * @return \DateTimeImmutable Publication date & time
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Return the object hash of this revision
     *
     * @return string Object hash
     */
    public function getHash()
    {
        return $this->hash;
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
        if ($this->published instanceof \DateTimeImmutable) {
            throw new RuntimeException(
                'Cannot republish object previously published at '.$this->published->format('c'),
                RuntimeException::CANNOT_REPUBLISH_OBJECT
            );
        }

        $systemProperties = clone $this;
        $systemProperties->published = new \DateTimeImmutable();
        return $systemProperties;
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

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
            self::PROPERTY_CREATED => $this->created->format('c'),
            self::PROPERTY_PUBLISHED => $this->published->format('c'),
            self::PROPERTY_HASH => $this->hash,
        ]);
    }
}
