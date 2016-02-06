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
    protected $_id = 0;

    /**
     * Object type
     *
     * @var Type
     */
    protected $_type = null;

    /**
     * Object revision
     *
     * @var Revision
     */
    protected $_revision = null;

    /**
     * Creation date
     *
     * @var \DateTimeImmutable
     */
    protected $_created = null;

    /**
     * Publication date
     *
     * @var \DateTimeImmutable
     */
    protected $_published = null;

    /**
     * Object hash
     *
     * @var string
     */
    protected $_hash = null;

    /**
     * Collection name
     *
     * @var string
     */
    const COLLECTION = 'system';

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
        if (array_key_exists('id', $data)) {
            $this->_setId(Id::unserialize($data['id']));
        }

        // Initialize the object type
        if (array_key_exists('type', $data)) {
            $this->_setType(Type::unserialize($data['type']));
        }

        // Initialize the object revision
        if (array_key_exists('revision', $data)) {
            $this->_setRevision(Revision::unserialize($data['revision']));
        }

        // Initialize the object creation date
        if (array_key_exists('created', $data)) {
            $this->_setCreated(new \DateTimeImmutable('@'.$data['created']));
        }

        // Initialize the object publication date
        if (array_key_exists('published', $data)) {
            $this->_setPublished(new \DateTimeImmutable('@'.$data['published']));
        }

        // Initialize the object hash
        if (array_key_exists('hash', $data)) {
            $this->_setHash($data['hash']);
        }
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision()
    {
        return $this->_revision;
    }

    /**
     * Return the creation date & time
     *
     * @return \DateTimeImmutable Creation date & time
     */
    public function getCreated()
    {
        return $this->_created;
    }

    /**
     * Return the publication date & time
     *
     * @return \DateTimeImmutable Publication date & time
     */
    public function getPublished()
    {
        return $this->_published;
    }

    /**
     * Return the object hash
     *
     * @return string Object hash
     */
    public function getHash()
    {
        return $this->_hash;
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Set the object ID
     *
     * @param Id $id
     */
    protected function _setId(Id $id)
    {
        $this->_id = $id;
    }

    /**
     * Set the object type
     *
     * @param Type $type Object type
     */
    protected function _setType(Type $type)
    {
        $this->_type = $type;
    }

    /**
     * Set the object revision
     *
     * @param Revision $revision Object revision
     */
    protected function _setRevision(Revision $revision)
    {
        $this->_revision = $revision;
    }

    /**
     * Set the publication date & time
     *
     * @param \DateTimeImmutable $published Publication date & time
     */
    protected function _setPublished(\DateTimeImmutable $published)
    {
        $this->_published = $published;
    }

    /**
     * Set the creation date & time
     *
     * @param \DateTimeImmutable $published Creation date & time
     */
    protected function _setCreated(\DateTimeImmutable $created)
    {
        $this->_created = $created;
    }

    /**
     * Set the object hash
     *
     * @param string $hash Object hash
     */
    protected function _setHash($hash)
    {
        $this->_hash = $hash;
    }
}
