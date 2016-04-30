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

use Apparat\Object\Domain\Factory\AuthorFactory;
use Apparat\Object\Domain\Model\Author\AuthorInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Meta object properties collection
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class MetaProperties extends AbstractProperties
{
    /**
     * Collection name
     *
     * @var string
     */
    const COLLECTION = 'meta';
    /**
     * Description property
     *
     * @var string
     */
    const PROPERTY_DESCRIPTION = 'description';
    /**
     * Abstract property
     *
     * @var string
     */
    const PROPERTY_ABSTRACT = 'abstract';
    /**
     * Keywords property
     *
     * @var string
     */
    const PROPERTY_KEYWORDS = 'keywords';
    /**
     * Categories property
     *
     * @var string
     */
    const PROPERTY_CATEGORIES = 'categories';
    /**
     * Authors property
     *
     * @var string
     */
    const PROPERTY_AUTHORS = 'authors';
    /**
     * Object description
     *
     * @var string
     */
    protected $description = '';
    /**
     * Object abstract
     *
     * @var string
     */
    protected $abstract = '';
    /**
     * Object keywords
     *
     * @var array
     */
    protected $keywords = [];
    /**
     * Object categories
     *
     * @var array
     */
    protected $categories = [];
    /**
     * Object authors
     *
     * @var AuthorInterface[]
     */
    protected $authors = [];

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Meta properties constructor
     *
     * @param array $data Property data
     * @param ObjectInterface $object Owner object
     */
    public function __construct(array $data, ObjectInterface $object)
    {
        parent::__construct($data, $object);

        // Initialize the description
        if (array_key_exists(self::PROPERTY_DESCRIPTION, $data)) {
            $this->description = $data[self::PROPERTY_DESCRIPTION];
        }

        // Initialize the abstract
        if (array_key_exists(self::PROPERTY_ABSTRACT, $data)) {
            $this->abstract = $data[self::PROPERTY_ABSTRACT];
        }

        // Initialize the keywords
        if (array_key_exists(self::PROPERTY_KEYWORDS, $data)) {
            $this->keywords = $this->normalizeSortedPropertyValues((array)$data[self::PROPERTY_KEYWORDS]);
        }

        // Initialize the categories
        if (array_key_exists(self::PROPERTY_CATEGORIES, $data)) {
            $this->categories = $this->normalizeSortedPropertyValues((array)$data[self::PROPERTY_CATEGORIES]);
        }

        // Initialize the authors
        if (array_key_exists(self::PROPERTY_AUTHORS, $data)) {
            $this->setAuthors($data[self::PROPERTY_AUTHORS]);
        }
    }

    /**
     * Return the object description
     *
     * @return string Object description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the object description
     *
     * @param string $description Object description
     * @return MetaProperties Self reference
     */
    public function setDescription($description)
    {
        return $this->mutateStringProperty(self::PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Return the object abstract
     *
     * @return string Object abstract
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * Set the object abstract
     *
     * @param string $abstract Object abstract
     * @return MetaProperties Self reference
     */
    public function setAbstract($abstract)
    {
        return $this->mutateStringProperty(self::PROPERTY_ABSTRACT, $abstract);
    }

    /**
     * Return the object keywords
     *
     * @return array Object keywords
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set the object keywords
     *
     * @param array $keywords Object keywords
     * @return MetaProperties Self reference
     */
    public function setKeywords(array $keywords)
    {
        return $this->mutateListProperty(self::PROPERTY_KEYWORDS, $this->normalizeSortedPropertyValues($keywords));
    }

    /**
     * Return the object categories
     *
     * @return array Object categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set the object categories
     *
     * @param array $categories Object categories
     * @return MetaProperties Self reference
     */
    public function setCategories(array $categories)
    {
        return $this->mutateListProperty(self::PROPERTY_CATEGORIES, $this->normalizeSortedPropertyValues($categories));
    }

    /**
     * Return the object authors
     *
     * @return AuthorInterface[]
     */
    public function getAuthors()
    {
        return $this->authors;
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
        /** @var AuthorInterface[] $newAuthors */
        $newAuthors = [];

        // Run through and validate all authors
        foreach ($authors as $author) {
            // If the author is invalid
            if (is_string($author)) {
                $author = AuthorFactory::createFromString(
                    $author,
                    $this->getObject()->getRepositoryPath()->getRepository()
                );
            }

            // If the author is invalid
            if (!$author instanceof AuthorInterface) {
                throw new InvalidArgumentException(
                    'Invalid object author',
                    InvalidArgumentException::INVALID_OBJECT_AUTHOR
                );
            }

            $newAuthors[$author->getSignature()] = $author;
        }

        $this->authors = array_values($newAuthors);
        return $this;
    }

    /**
     * Return the property values as array
     *
     * @return array Property values
     */
    public function toArray()
    {
        return array_filter([
            self::PROPERTY_DESCRIPTION => $this->description,
            self::PROPERTY_ABSTRACT => $this->abstract,
            self::PROPERTY_KEYWORDS => $this->keywords,
            self::PROPERTY_CATEGORIES => $this->categories,
            self::PROPERTY_AUTHORS => array_map(
                function (AuthorInterface $author) {
                    return $author->serialize();
                },
                $this->authors
            )
        ]);
    }
}
