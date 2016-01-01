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

use Apparat\Object\Domain\Model\Author\AuthorInterface;
use Apparat\Object\Domain\Factory\AuthorFactory;

/**
 * Meta object properties collection
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class MetaProperties implements PropertiesInterface
{
	/**
	 * Object keywords
	 *
	 * @var array
	 */
	protected $_keywords = [];

	/**
	 * Object categories
	 *
	 * @var array
	 */
	protected $_categories = [];

	/**
	 * Object authors
	 *
	 * @var AuthorInterface[]
	 */
	protected $_authors = [];

	/**
	 * Collection name
	 *
	 * @var string
	 */
	const COLLECTION = 'meta';

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * System properties constructor
	 *
	 * @param array $data System properties
	 */
	public function __construct(array $data)
	{
		// Initialize the keywords
		if (array_key_exists('keywords', $data)) {
			$this->setKeywords((array)$data['keywords']);
		}

		// Initialize the categories
		if (array_key_exists('categories', $data)) {
			$this->setCategories((array)$data['categories']);
		}

		// Initialize the authors
		if (array_key_exists('authors', $data)) {
			$this->setAuthors($data['authors']);
		}
	}

	/**
	 * Return the object keywords
	 *
	 * @return array Object keywords
	 */
	public function getKeywords()
	{
		return $this->_keywords;
	}

	/**
	 * Set the object keywords
	 *
	 * @param array $keywords Object keywords
	 * @return MetaProperties Self reference
	 */
	public function setKeywords(array $keywords)
	{
		$this->_keywords = array_unique($keywords);
		sort($this->_keywords, SORT_NATURAL);
		return $this;
	}

	/**
	 * Return the object categories
	 *
	 * @return array Object categories
	 */
	public function getCategories()
	{
		return $this->_categories;
	}

	/**
	 * Set the object categories
	 *
	 * @param array $categories Object categories
	 * @return MetaProperties Self reference
	 */
	public function setCategories(array $categories)
	{
		$this->_categories = array_unique($categories);
		sort($this->_categories, SORT_NATURAL);
		return $this;
	}

	/**
	 * Return the object authors
	 *
	 * @return AuthorInterface[]
	 */
	public function getAuthors()
	{
		return $this->_authors;
	}

	/**
	 * Set the object authors
	 *
	 * @param array $authors Object authors
	 * @return MetaProperties Self reference
	 * @throws InvalidArgumentException If an author is invalid
	 */
	public function setAuthors(array $authors)
	{
		$newAuthors = [];

		// Run through and validate all authors
		foreach ($authors as $author) {

			// If the author is invalid
			if (is_string($author)) {
				$author = AuthorFactory::createFromString($author);
			}

			// If the author is invalid
			if (!$author instanceof AuthorInterface) {
				throw new InvalidArgumentException('Invalid object author',
					InvalidArgumentException::INVALID_OBJECT_AUTHOR);
			}

			$newAuthors[$author->getSignature()] = $author;
		}

		$this->_authors = array_values($newAuthors);
		return $this;
	}

}