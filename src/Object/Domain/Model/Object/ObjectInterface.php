<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Object\Domain
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
use Apparat\Object\Domain\Model\Path\RepositoryPath;

/**
 * Object interface
 *
 * @package Apparat\Object\Domain\Model\Object
 */
interface ObjectInterface
{
	/**
	 * Object constructor
	 *
	 * @param RepositoryPath $path Object repository path
	 * @param array $propertyData Property data
	 * @param string $payload Object payload
	 */
	public function __construct(RepositoryPath $path, array $propertyData = [], $payload = '');

	/**
	 * Return the object ID
	 *
	 * @return Id Object ID
	 */
	public function getId();

	/**
	 * Return the object type
	 *
	 * @return Type Object type
	 */
	public function getType();

	/**
	 * Return the object revision
	 *
	 * @return Revision Object revision
	 */
	public function getRevision();

	/**
	 * Return the creation date & time
	 *
	 * @return \DateTimeImmutable Creation date & time
	 */
	public function getCreated();

	/**
	 * Return the publication date & time
	 *
	 * @return \DateTimeImmutable Publication date & time
	 */
	public function getPublished();

	/**
	 * Return all object keywords
	 *
	 * @return array Object keywords
	 */
	public function getKeywords();

	/**
	 * Return all object categories
	 *
	 * @return array Object categories
	 */
	public function getCategories();

	/**
	 * Return all object authors
	 *
	 * @return AuthorInterface[] Authors
	 */
	public function getAuthors();

	/**
	 * Add an object author
	 *
	 * @param AuthorInterface $author Author
	 * @return ObjectInterface Self reference
	 */
	public function addAuthor(AuthorInterface $author);
}