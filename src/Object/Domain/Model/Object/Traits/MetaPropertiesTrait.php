<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Model\Object\Traits;

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\MetaProperties;

/**
 * Meta properties trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 * @property array $collectionStates
 */
trait MetaPropertiesTrait
{
    /**
     * Meta properties
     *
     * @var MetaProperties
     */
    protected $metaProperties;

    /**
     * Return the object title
     *
     * @return string Object title
     */
    public function getTitle()
    {
        return $this->metaProperties->getTitle();
    }

    /**
     * Set the title
     *
     * @param string $title Title
     * @return ObjectInterface Self reference
     */
    public function setTitle($title)
    {
        $this->setMetaProperties($this->metaProperties->setTitle($title));
        return $this;
    }

    /**
     * Set the meta properties collection
     *
     * @param MetaProperties $metaProperties Meta property collection
     * @param bool $overwrite Overwrite the existing collection (if present)
     */
    protected function setMetaProperties(MetaProperties $metaProperties, $overwrite = false)
    {
        $this->metaProperties = $metaProperties;
        $metaPropertiesState = spl_object_hash($this->metaProperties);

        // If the meta property collection state has changed
        if (!$overwrite
            && !empty($this->collectionStates[MetaProperties::COLLECTION])
            && ($metaPropertiesState !== $this->collectionStates[MetaProperties::COLLECTION])
        ) {
            // Flag this object as mutated
            $this->setMutatedState();
        }

        $this->collectionStates[MetaProperties::COLLECTION] = $metaPropertiesState;
    }

    /**
     * Return the object slug
     *
     * @return string Object slug
     */
    public function getSlug()
    {
        return $this->metaProperties->getSlug();
    }

    /**
     * Set the slug
     *
     * @param string $slug Slug
     * @return ObjectInterface Self reference
     */
    public function setSlug($slug)
    {
        $this->setMetaProperties($this->metaProperties->setSlug($slug));
        return $this;
    }

    /**
     * Return the object description
     *
     * @return string Object description
     */
    public function getDescription()
    {
        return $this->metaProperties->getDescription();
    }

    /**
     * Set the description
     *
     * @param string $description Description
     * @return ObjectInterface Self reference
     */
    public function setDescription($description)
    {
        $this->setMetaProperties($this->metaProperties->setDescription($description));
        return $this;
    }

    /**
     * Return the object abstract
     *
     * @return string Object abstract
     */
    public function getAbstract()
    {
        return $this->metaProperties->getAbstract();
    }

    /**
     * Set the abstract
     *
     * @param string $abstract Abstract
     * @return ObjectInterface Self reference
     */
    public function setAbstract($abstract)
    {
        $this->setMetaProperties($this->metaProperties->setAbstract($abstract));
        return $this;
    }

    /**
     * Return the license
     *
     * @return string License
     */
    public function getLicense()
    {
        return $this->metaProperties->getLicense();
    }

    /**
     * Set the license
     *
     * @param string $license License
     * @return MetaProperties Self reference
     */
    public function setLicense($license)
    {
        $this->setMetaProperties($this->metaProperties->setLicense($license));
        return $this;
    }

    /**
     * Return the privacy
     *
     * @return string Privacy
     */
    public function getPrivacy()
    {
        return $this->metaProperties->getPrivacy();
    }

    /**
     * Set the privacy
     *
     * @param string $privacy Privacy
     * @return MetaProperties Self reference
     */
    public function setPrivacy($privacy)
    {
        $this->setMetaProperties($this->metaProperties->setPrivacy($privacy));
        return $this;
    }

    /**
     * Return all object keywords
     *
     * @return array Object keywords
     */
    public function getKeywords()
    {
        return $this->metaProperties->getKeywords();
    }

    /**
     * Set the keywords
     *
     * @param array $keywords Keywords
     * @return ObjectInterface Self reference
     */
    public function setKeywords(array $keywords)
    {
        $this->setMetaProperties($this->metaProperties->setKeywords($keywords));
        return $this;
    }

    /**
     * Return all object categories
     *
     * @return array Object categories
     */
    public function getCategories()
    {
        return $this->metaProperties->getCategories();
    }

    /**
     * Set the categories
     *
     * @param array $categories Categories
     * @return ObjectInterface Self reference
     */
    public function setCategories(array $categories)
    {
        $this->setMetaProperties($this->metaProperties->setCategories($categories));
        return $this;
    }

    /**
     * Set the object state to mutated
     */
    abstract protected function setMutatedState();
}
