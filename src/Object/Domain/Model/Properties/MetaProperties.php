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
     * Title property
     *
     * @var string
     */
    const PROPERTY_TITLE = 'title';
    /**
     * Slug property
     *
     * @var string
     */
    const PROPERTY_SLUG = 'slug';
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
     * License property
     *
     * @var string
     */
    const PROPERTY_LICENSE = 'license';
    /**
     * Privacy property
     *
     * @var string
     */
    const PROPERTY_PRIVACY = 'privacy';
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
     * Private
     *
     * @var string
     */
    const PRIVACY_PRIVATE = 'private';
    /**
     * Public
     *
     * @var string
     */
    const PRIVACY_PUBLIC = 'public';
    /**
     * Object title
     *
     * @var string
     */
    protected $title = '';
    /**
     * Object slug
     *
     * @var string
     */
    protected $slug = '';
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
     * Object license
     *
     * @var string
     */
    protected $license = '';
    /**
     * Object privacy
     *
     * @var string
     */
    protected $privacy = self::PRIVACY_PRIVATE;
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
     * Privacy levels
     *
     * @var array
     */
    protected static $privacyLevels = [
        self::PRIVACY_PRIVATE,
        self::PRIVACY_PUBLIC,
    ];

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

        // Initialize the title
        if (array_key_exists(self::PROPERTY_TITLE, $data)) {
            $this->title = $data[self::PROPERTY_TITLE];
        }

        // Initialize the slug
        if (array_key_exists(self::PROPERTY_SLUG, $data)) {
            $this->slug = $data[self::PROPERTY_SLUG];
        }

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
    }

    /**
     * Return the object title
     *
     * @return string Object title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the object title
     *
     * @param string $title Object title
     * @return MetaProperties Self reference
     */
    public function setTitle($title)
    {
        return $this->mutateStringProperty(self::PROPERTY_TITLE, $title);
    }

    /**
     * Return the object slug
     *
     * @return string Object slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the object slug
     *
     * @param string $slug Object slug
     * @return MetaProperties Self reference
     */
    public function setSlug($slug)
    {
        return $this->mutateStringProperty(self::PROPERTY_SLUG, $slug);
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
     * Return the object license
     *
     * @return string Object license
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * Set the object license
     *
     * @param string $license Object license
     * @return MetaProperties Self reference
     */
    public function setLicense($license)
    {
        return $this->mutateStringProperty(self::PROPERTY_LICENSE, $license);
    }

    /**
     * Return the object privacy
     *
     * @return string Object privacy
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * Set the object privacy
     *
     * @param string $privacy Object privacy
     * @return MetaProperties Self reference
     */
    public function setPrivacy($privacy)
    {
        // If the privacy level is unknown
        if (!in_array($privacy, self::$privacyLevels)) {
            throw new OutOfBoundsException(
                sprintf('Invalid privacy level "%s"', $privacy),
                OutOfBoundsException::INVALID_PRIVACY_LEVEL
            );
        }

        return $this->mutateStringProperty(self::PROPERTY_PRIVACY, $privacy);
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
        ]);
    }
}
