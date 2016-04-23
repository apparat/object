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

use Apparat\Kernel\Ports\Kernel;
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
    protected $localPath = null;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Object URL constructor
     *
     * @param string $url Object URL
     * @param boolean $remote Accept remote URL (less strict date component checking)
     * @throws InvalidArgumentException If remote URLs are not allowed and a remote URL is given
     * @throws InvalidArgumentException If the path component is empty
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

        // If the path component is empty
        if (empty($this->urlParts['path'])) {
            throw new InvalidArgumentException(
                'Invalid object URL path (empty)',
                InvalidArgumentException::INVALID_OBJECT_URL_PATH
            );
        }

        // Instantiate the local path component
        $this->localPath = new LocalPath(
            $this->urlParts['path'],
            $remote ? true : null,
            $this->urlParts['path']
        );

        // Normalize the path prefix
        if (!strlen($this->urlParts['path'])) {
            $this->urlParts['path'] = null;
        }
    }

    /**
     * Set the object's creation date
     *
     * @param \DateTimeImmutable $creationDate
     * @return PathInterface|ObjectUrl New object path
     */
    public function setCreationDate(\DateTimeImmutable $creationDate)
    {
        $this->localPath = $this->localPath->setCreationDate($creationDate);
        return $this;
    }

    /**
     * Set the object type
     *
     * @param Type $type Object type
     * @return PathInterface|ObjectUrl New object URL
     */
    public function setType(Type $type)
    {
        $this->localPath = $this->localPath->setType($type);
        return $this;
    }

    /**
     * Set the object ID
     *
     * @param Id $uid Object ID
     * @return PathInterface|ObjectUrl New object URL
     */
    public function setId(Id $uid)
    {
        $this->localPath = $this->localPath->setId($uid);
        return $this;
    }

    /**
     * Set the object revision
     *
     * @param Revision $revision Object revision
     * @return PathInterface|ObjectUrl New object URL
     */
    public function setRevision(Revision $revision)
    {
        $this->localPath = $this->localPath->setRevision($revision);
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
     * Return the object's creation date
     *
     * @return \DateTimeImmutable Object creation date
     */
    public function getCreationDate()
    {
        return $this->localPath->getCreationDate();
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->localPath->getId();
    }

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getType()
    {
        return $this->localPath->getType();
    }

    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision()
    {
        return $this->localPath->getRevision();
    }

    /**
     * Return the local object path
     *
     * @return PathInterface|LocalPath Local object path
     */
    public function getLocalPath()
    {
        return $this->localPath;
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
            $baseUrl = Kernel::create(Url::class, [getenv('APPARAT_BASE_URL')]);
            return substr($this->getPath(), strlen($baseUrl->getPath()));

            // Else: If it's a relative URL: Extract the repository URL
        } elseif (!$this->isAbsolute()) {
            return $this->getPath();
        }

        // Else: It must be a remote repository
        $override = [
            'object' => '',
            'query' => '',
            'fragment' => '',
        ];
        return $this->getUrlInternal($override);
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
    protected function getUrlInternal(array &$override = [])
    {
        parent::getUrlInternal($override);

        // Prepare the local object path
        $override['object'] = isset($override['object']) ? $override['object'] : strval($this->localPath);

        return "{$override['scheme']}{$override['user']}{$override['pass']}{$override['host']}{$override['port']}".
        "{$override['path']}{$override['object']}{$override['query']}{$override['fragment']}";
    }
}
