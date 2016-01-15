<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Model\Object;

use Apparat\Object\Domain\Model\Author\AuthorInterface;
use Apparat\Object\Domain\Model\Path\ApparatUrl;
use Apparat\Object\Domain\Model\Path\ObjectUrl;
use Apparat\Object\Domain\Model\Path\PathInterface;

/**
 * Object proxy (lazy loading)
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class ObjectProxy implements ObjectInterface
{
	/**
	 * Apparat object URL
	 *
	 * @var ApparatUrl
	 */
	protected $_url = null;
	/**
	 * Object
	 *
	 * @var ObjectInterface
	 */
	protected $_object = null;

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Object proxy constructor
	 *
	 * @param ApparatUrl $url Apparat object URL
	 */
	public function __construct(ApparatUrl $url)
	{
		$this->_url = $url;
	}

	/**
	 * Return the object repository path
	 *
	 * @return PathInterface Object repository path
	 */
	public function getRepositoryPath()
	{
		// If the object has already been instatiated
		if ($this->_object instanceof ObjectInterface) {
			return $this->_object->getRepositoryPath();

			// Else
		} else {
			return $this->_url->getLocalPath();
		}
	}

	/**
	 * Return the object ID
	 *
	 * @return Id Object ID
	 */
	public function getId()
	{
		return $this->_object()->getId();
	}

	/**
	 * Return the object type
	 *
	 * @return Type Object type
	 */
	public function getType()
	{
		return $this->_object()->getType();
	}

	/**
	 * Return the object revision
	 *
	 * @return Revision Object revision
	 */
	public function getRevision()
	{
		return $this->_object()->getRevision();
	}

	/**
	 * Return the creation date & time
	 *
	 * @return \DateTimeImmutable Creation date & time
	 */
	public function getCreated()
	{
		return $this->_object()->getCreated();
	}

	/**
	 * Return the publication date & time
	 *
	 * @return \DateTimeImmutable Publication date & time
	 */
	public function getPublished()
	{
		return $this->_object()->getPublished();
	}

	/**
	 * Return all object keywords
	 *
	 * @return array Object keywords
	 */
	public function getKeywords()
	{
		return $this->_object()->getKeywords();
	}

	/**
	 * Return all object categories
	 *
	 * @return array Object categories
	 */
	public function getCategories()
	{
		return $this->_object()->getCategories();
	}

	/**
	 * Return all object authors
	 *
	 * @return AuthorInterface[] Authors
	 */
	public function getAuthors()
	{
		return $this->_object()->getAuthors();
	}

	/**
	 * Add an object author
	 *
	 * @param AuthorInterface $author Author
	 * @return ObjectInterface Self reference
	 */
	public function addAuthor(AuthorInterface $author)
	{
		return $this->_object()->addAuthor($author);
	}

	/**
	 * Return the absolute object URL
	 *
	 * @return string
	 */
	public function getAbsoluteUrl()
	{
		return strval($this->_url);
	}

	/*******************************************************************************
	 * MAGIG METHODS
	 *******************************************************************************/

	/**
	 * Generic caller
	 *
	 * @param string $name Method name
	 * @param array $arguments Method arguments
	 */
	public function __call($name, $arguments)
	{
		$object = $this->_object();
		if (is_callable(array($object, $name))) {
			return $object->$name(...$arguments);
		}

		throw new InvalidArgumentException(sprintf('Invalid object proxy method "%s"', $name),
			InvalidArgumentException::INVALID_OBJECT_PROXY_METHOD);
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Return the enclosed remote object
	 *
	 * @return ObjectInterface Remote object
	 */
	protected function _object()
	{
		// Lazy-load the remote object if necessary
		if (!$this->_object instanceof ObjectInterface) {
			echo $this->getAbsoluteUrl();
		}

		return $this->_object;
	}
}