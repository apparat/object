<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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
use Apparat\Object\Domain\Model\Object\Type;

/**
 * System object properties collection
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class SystemProperties extends AbstractProperties
{
    /**
     * Object ID
     *
     * @var Id
     */
    protected $id = 0;

    /**
     * Object type
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
     * Creation date
     *
     * @var \DateTimeImmutable
     */
    protected $created = null;

    /**
     * Publication date
     *
     * @var \DateTimeImmutable
     */
    protected $published = null;

    /**
     * Object hash
     *
     * @var string
     */
    protected $hash = null;

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
            $this->setId(Id::unserialize($data[self::PROPERTY_ID]));
        }

        // Initialize the object type
        if (array_key_exists(self::PROPERTY_TYPE, $data)) {
            $this->setType(Type::unserialize($data[self::PROPERTY_TYPE]));
        }

        // Initialize the object revision
        if (array_key_exists(self::PROPERTY_REVISION, $data)) {
            $this->setRevision(Revision::unserialize($data[self::PROPERTY_REVISION]));
        }

        // Initialize the object creation date
        if (array_key_exists(self::PROPERTY_CREATED, $data)) {
            $this->setCreated(new \DateTimeImmutable('@'.$data[self::PROPERTY_CREATED]));
        }

        // Initialize the object publication date
        if (array_key_exists(self::PROPERTY_PUBLISHED, $data)) {
            $this->setPublished(new \DateTimeImmutable('@'.$data[self::PROPERTY_PUBLISHED]));
        }

        // Initialize the object hash
        if (array_key_exists(self::PROPERTY_HASH, $data)) {
            $this->setHash($data[self::PROPERTY_HASH]);
        }
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->id;
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
     * Return the creation date & time
     *
     * @return \DateTimeImmutable Creation date & time
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Return the publication date & time
     *
     * @return \DateTimeImmutable Publication date & time
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Return the object hash
     *
     * @return string Object hash
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Return the property values as array
     *
     * @return array Property values
     */
    public function toArray()
    {
        return [
            self::PROPERTY_ID => $this->id->getId(),
            self::PROPERTY_TYPE => $this->type->getType(),
            self::PROPERTY_REVISION => $this->revision->getRevision(),
            self::PROPERTY_CREATED => $this->created->format('c'),
            self::PROPERTY_PUBLISHED => $this->published->format('c'),
            self::PROPERTY_HASH => $this->hash,
        ];
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Set the object ID
     *
     * @param Id $id
     */
    protected function setId(Id $id)
    {
        $this->id = $id;
    }

    /**
     * Set the object type
     *
     * @param Type $type Object type
     */
    protected function setType(Type $type)
    {
        $this->type = $type;
    }

    /**
     * Set the object revision
     *
     * @param Revision $revision Object revision
     */
    protected function setRevision(Revision $revision)
    {
        $this->revision = $revision;
    }

    /**
     * Set the publication date & time
     *
     * @param \DateTimeImmutable $published Publication date & time
     */
    protected function setPublished(\DateTimeImmutable $published)
    {
        $this->published = $published;
    }

    /**
     * Set the creation date & time
     *
     * @param \DateTimeImmutable $published Creation date & time
     */
    protected function setCreated(\DateTimeImmutable $created)
    {
        $this->created = $created;
    }

    /**
     * Set the object hash
     *
     * @param string $hash Object hash
     */
    protected function setHash($hash)
    {
        $this->hash = $hash;
    }
}
