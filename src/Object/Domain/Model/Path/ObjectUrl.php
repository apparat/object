<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Model\Path;

use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * Object URL
 *
 * @package Apparat\Object\Domain\Model
 */
class ObjectUrl extends Url implements PathInterface
{
    /**
     * Object path
     *
     * @var LocalPath
     */
    protected $_localPath = null;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Object URL constructor
     *
     * @param string $url Object URL
     * @param boolean $remote Accept remote URL (less strict date component checking)
     * @throws InvalidArgumentException If remote URLs are not allowed and a remote URL is given
     */
    public function __construct($url, $remote = false)
    {
        parent::__construct($url);

        // If it's an invalid remote object URL
        if ($this->isAbsolute() && !$remote) {
            throw new InvalidArgumentException(
                sprintf('Unallowed remote object URL "%s"', $url),
                InvalidArgumentException::UNALLOWED_REMOTE_OBJECT_URL
            );
        }

        // Instantiate the local path component
        $this->_localPath = new LocalPath(
            empty($this->_urlParts['path']) ? '' : $this->_urlParts['path'],
            $remote ? true : null, $this->_urlParts['path']
        );

        // Normalize the path prefix
        if (!strlen($this->_urlParts['path'])) {
            $this->_urlParts['path'] = null;
        }
    }

    /**
     * Return the object's creation date
     *
     * @return \DateTimeImmutable Object creation date
     */
    public function getCreationDate()
    {
        return $this->_localPath->getCreationDate();
    }

    /**
     * Set the object's creation date
     *
     * @param \DateTimeImmutable $creationDate
     * @return LocalPath New object path
     */
    public function setCreationDate(\DateTimeImmutable $creationDate)
    {
        $this->_localPath = $this->_localPath->setCreationDate($creationDate);
        return $this;
    }

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getType()
    {
        return $this->_localPath->getType();
    }

    /**
     * Set the object type
     *
     * @param Type $type Object type
     * @return ObjectUrl New object URL
     */
    public function setType(Type $type)
    {
        $this->_localPath = $this->_localPath->setType($type);
        return $this;
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->_localPath->getId();
    }

    /**
     * Set the object ID
     *
     * @param Id $id Object ID
     * @return ObjectUrl New object URL
     */
    public function setId(Id $id)
    {
        $this->_localPath = $this->_localPath->setId($id);
        return $this;
    }


    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision()
    {
        return $this->_localPath->getRevision();
    }

    /**
     * Set the object revision
     *
     * @param Revision $revision Object revision
     * @return ObjectUrl New object URL
     */
    public function setRevision(Revision $revision)
    {
        $this->_localPath = $this->_localPath->setRevision($revision);
        return $this;
    }

    /**
     * Test if this URL matches all available parts of a given URL
     *
     * @param Url $url Comparison URL
     * @return bool This URL matches all available parts of the given URL
     */
    public function matches(Url $url)
    {
        // If the standard URL components don't match
        if (!parent::matches($url)) {
            return false;
        }

        // Extended tests if it's an object URL
        if ($url instanceof self) {
            // Test the object creation date
            if ($this->getCreationDate() != $url->getCreationDate()) {
                return false;
            }

            // Test the object ID
            if ($this->getId()->serialize() !== $url->getId()->serialize()) {
                return false;
            }

            // Test the object type
            if ($this->getType()->serialize() !== $url->getType()->serialize()) {
                return false;
            }

            // Test the object revision
            if ($this->getRevision()->serialize() !== $url->getRevision()->serialize()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Return the local object path
     *
     * @return LocalPath Local object path
     */
    public function getLocalPath()
    {
        return $this->_localPath;
    }

    /**
     * Return the repository URL part of this object URL
     *
     * @return string Repository URL
     * @see https://github.com/apparat/apparat/blob/master/doc/URL-DESIGN.md#repository-url
     */
    public function getRepositoryUrl()
    {
        // If the object URL is absolute and local: Extract the repository URL
        if ($this->isAbsoluteLocal()) {
            $repositoryUrl = substr($this->getPath(), strlen((new Url(getenv('APPARAT_BASE_URL')))->getPath()));

            // Else: If it's a relative URL: Extract the repository URL
        } elseif (!$this->isAbsolute()) {
            $repositoryUrl = $this->getPath();

            // Else: It must be a remote repository
        } else {
            $override = [
                'object' => '',
                'query' => '',
                'fragment' => '',
            ];
            $repositoryUrl = $this->_getUrl($override);
        }

        return $repositoryUrl;
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Return the a complete serialized object URL
     *
     * @param array $override Override components
     * @return string Serialized URL
     */
    protected function _getUrl(array &$override = [])
    {
        parent::_getUrl($override);

        // Prepare the local object path
        if (isset($override['object'])) {
            $object = $override['object'];
        } else {
            $object = strval($this->_localPath);
        }
        $override['object'] = $object;

        return "{$override['scheme']}{$override['user']}{$override['pass']}{$override['host']}{$override['port']}{$override['path']}{$override['object']}{$override['query']}{$override['fragment']}";
    }
}
