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

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\InvalidArgumentException;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Relation\RelationInterface;
use Apparat\Object\Domain\Model\Uri\ApparatUrl;
use Apparat\Object\Domain\Model\Uri\RepositoryLocatorInterface;
use Apparat\Object\Domain\Repository\Service;

/**
 * Object proxy (lazy loading)
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
trait ObjectProxyTrait
{
    /**
     * Use traits
     */
    use IterableProxyTrait;
    /**
     * Object
     *
     * @var ObjectInterface
     */
    protected $object = null;

    /**
     * Return the object repository locator
     *
     * @return RepositoryLocatorInterface Object repository locator
     */
    public function getRepositoryLocator()
    {
        // If the object has already been instantiated
        if ($this->object instanceof ObjectInterface) {
            return $this->object->getRepositoryLocator();
        }

        return $this->getUrl()->getLocator();
    }

    /**
     * Return the object property data
     *
     * @param bool $serialize Serialize property objects
     * @return array Object property data
     */
    public function getPropertyData($serialize = true)
    {
        return $this->object()->getPropertyData($serialize);
    }

    /**
     * Return the enclosed remote object
     *
     * @return ObjectInterface Remote object
     */
    protected function object()
    {
        // Lazy-load the remote object if necessary
        if (!$this->object instanceof ObjectInterface) {
            // Instantiate the local object repository, load and return the object
            $this->object = Kernel::create(Service::class)->get($this->getUrl())->loadObject($this->getUrl()->getLocator());
        }

        return $this->object;
    }

    /**
     * Return the object payload
     *
     * @return string Object payload
     */
    public function getPayload()
    {
        return $this->object()->getPayload();
    }

    /**
     * Set the payload
     *
     * @param string $payload Payload
     * @return ObjectInterface Self reference
     */
    public function setPayload($payload)
    {
        return $this->object()->setPayload($payload);
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->object()->getId();
    }

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getObjectType()
    {
        return $this->object()->getObjectType();
    }

    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision()
    {
        return $this->object()->getRevision();
    }

    /**
     * Return the latitude
     *
     * @return float Latitude
     */
    public function getLatitude()
    {
        return $this->object()->getLatitude();
    }

    /**
     * Set the latitude
     *
     * @param float $latitude Latitude
     * @return ObjectInterface Self reference
     */
    public function setLatitude($latitude)
    {
        return $this->object()->setLatitude($latitude);
    }

    /**
     * Return the longitude
     *
     * @return float Longitude
     */
    public function getLongitude()
    {
        return $this->object()->getLongitude();
    }

    /**
     * Set the longitude
     *
     * @param float $longitude Longitude
     * @return ObjectInterface Self reference
     */
    public function setLongitude($longitude)
    {
        return $this->object()->setLongitude($longitude);
    }

    /**
     * Return the elevation
     *
     * @return float Elevation
     */
    public function getElevation()
    {
        return $this->object()->getElevation();
    }

    /**
     * Set the elevation
     *
     * @param float $elevation
     * @return ObjectInterface Self reference
     */
    public function setElevation($elevation)
    {
        return $this->object()->setElevation($elevation);
    }


    /**
     * Return the object draft mode
     *
     * @return boolean Object draft mode
     */
    public function isDraft()
    {
        return $this->object()->isDraft();
    }

    /**
     * Return whether the object is in modified state
     *
     * @return boolean Modified state
     */
    public function hasBeenModified()
    {
        return $this->object()->hasBeenModified();
    }

    /**
     * Return whether the object is in mutated state
     *
     * @return boolean Mutated state
     */
    public function hasBeenMutated()
    {
        return $this->object()->hasBeenMutated();
    }

    /**
     * Return the creation date & time
     *
     * @return \DateTimeInterface Creation date & time
     */
    public function getCreated()
    {
        return $this->object()->getCreated();
    }

    /**
     * Return the deletion date & time
     *
     * @return \DateTimeInterface Deletion date & time
     */
    public function getDeleted()
    {
        return $this->object()->getDeleted();
    }

    /**
     * Return the modification date & time
     *
     * @return \DateTimeInterface Modification date & time
     */
    public function getModified()
    {
        return $this->object()->getModified();
    }

    /**
     * Return the publication date & time
     *
     * @return \DateTimeInterface Publication date & time
     */
    public function getPublished()
    {
        return $this->object()->getPublished();
    }

    /**
     * Return the object title
     *
     * @return string Object title
     */
    public function getTitle()
    {
        return $this->object()->getTitle();
    }

    /**
     * Set the title
     *
     * @param string $title Title
     * @return ObjectInterface Self reference
     */
    public function setTitle($title)
    {
        return $this->object()->setTitle($title);
    }

    /**
     * Return the object slug
     *
     * @return string Object slug
     */
    public function getSlug()
    {
        return $this->object()->getSlug();
    }

    /**
     * Set the slug
     *
     * @param string $slug Slug
     * @return ObjectInterface Self reference
     */
    public function setSlug($slug)
    {
        return $this->object()->setSlug($slug);
    }


    /**
     * Return the object description
     *
     * @return string Object description
     */
    public function getDescription()
    {
        return $this->object()->getDescription();
    }

    /**
     * Set the description
     *
     * @param string $description Description
     * @return ObjectInterface Self reference
     */
    public function setDescription($description)
    {
        return $this->object()->setDescription($description);
    }

    /**
     * Return the object abstract
     *
     * @return string Object abstract
     */
    public function getAbstract()
    {
        return $this->object()->getAbstract();
    }

    /**
     * Set the abstract
     *
     * @param string $abstract Abstract
     * @return ObjectInterface Self reference
     */
    public function setAbstract($abstract)
    {
        return $this->object()->setAbstract($abstract);
    }

    /**
     * Return all object keywords
     *
     * @return array Object keywords
     */
    public function getKeywords()
    {
        return $this->object()->getKeywords();
    }

    /**
     * Set the keywords
     *
     * @param array $keywords Keywords
     * @return ObjectInterface Self reference
     */
    public function setKeywords(array $keywords)
    {
        return $this->object()->setKeywords($keywords);
    }

    /**
     * Return the license
     *
     * @return string License
     */
    public function getLicense()
    {
        return $this->object()->getLicense();
    }

    /**
     * Set the license
     *
     * @param string $license License
     * @return ObjectInterface Self reference
     */
    public function setLicense($license)
    {
        return $this->object()->setLicense($license);
    }

    /**
     * Return the language
     *
     * @return string Language
     */
    public function getLanguage()
    {
        return $this->object()->getLanguage();
    }

    /**
     * Return the privacy
     *
     * @return string Privacy
     */
    public function getPrivacy()
    {
        return $this->object()->getPrivacy();
    }

    /**
     * Set the privacy
     *
     * @param string $privacy Privacy
     * @return ObjectInterface Self reference
     */
    public function setPrivacy($privacy)
    {
        return $this->object()->setPrivacy($privacy);
    }

    /**
     * Return all object categories
     *
     * @return array Object categories
     */
    public function getCategories()
    {
        return $this->object()->getCategories();
    }

    /**
     * Set the categories
     *
     * @param array $categories Categories
     * @return ObjectInterface Self reference
     */
    public function setCategories(array $categories)
    {
        return $this->object()->setCategories($categories);
    }

    /**
     * Get a domain property value
     *
     * Multi-level properties might be traversed by property name locators separated with colons (":").
     *
     * @param string $property Property name
     * @return mixed Property value
     */
    public function getDomain($property)
    {
        return $this->object()->getDomain($property);
    }

    /**
     * Set a domain property value
     *
     * @param string $property Property name
     * @param mixed $value Property value
     * @return ObjectInterface Self reference
     */
    public function setDomain($property, $value)
    {
        return $this->object()->setDomain($property, $value);
    }

    /**
     * Get a processing instruction
     *
     * @param string $procInst Processing instruction name
     * @return mixed Processing instruction
     */
    public function getProcessingInstruction($procInst)
    {
        return $this->object()->getProcessingInstruction($procInst);
    }

    /**
     * Set a processing instruction
     *
     * @param string $procInst Processing instruction name
     * @param mixed $value Processing instruction
     * @return ObjectInterface Self reference
     */
    public function setProcessingInstruction($procInst, $value)
    {
        return $this->object()->setProcessingInstruction($procInst, $value);
    }

    /**
     * Return the canonical object URL
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        return $this->getAbsoluteUrl();
    }

    /**
     * Return the absolute object URL
     *
     * @return string
     */
    public function getAbsoluteUrl()
    {
        return strval($this->getUrl());
    }

    /**
     * Generic caller
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     */
    public function __call($name, $arguments)
    {
        $object = $this->object();
        if (is_callable(array($object, $name))) {
            return $object->$name(...$arguments);
        }

        throw new InvalidArgumentException(
            sprintf('Invalid object proxy method "%s"', $name),
            InvalidArgumentException::INVALID_OBJECT_PROXY_METHOD
        );
    }

    /**
     * Use a specific object revision
     *
     * @param Revision $revision Revision to be used
     * @return ObjectInterface Object
     */
    public function useRevision(Revision $revision)
    {
        return $this->object()->useRevision($revision);
    }

    /**
     * Persist the current object revision
     *
     * @return ObjectInterface Object
     */
    public function persist()
    {
        return $this->object()->persist();
    }

    /**
     * Return whether the object is in published state
     *
     * @return boolean Published state
     */
    public function isPublished()
    {
        return $this->object()->isPublished();
    }

    /**
     * Return whether the object has just been published
     *
     * @return boolean Object has just been published
     */
    public function hasBeenPublished()
    {
        return $this->object()->hasBeenPublished();
    }

    /**
     * Return whether the object has been deleted
     *
     * @return boolean Object is deleted
     */
    public function isDeleted()
    {
        return $this->object()->isDeleted();
    }

    /**
     * Return whether the object has just been deleted
     *
     * @return boolean Object has just been deleted
     */
    public function hasBeenDeleted()
    {
        return $this->object()->hasBeenDeleted();
    }

    /**
     * Return whether the object has just been undeleted
     *
     * @return boolean Object has just been undeleted
     */
    public function hasBeenUndeleted()
    {
        return $this->object()->hasBeenUndeleted();
    }


    /**
     * Publish the current object revision
     *
     * @return ObjectInterface Object
     */
    public function publish()
    {
        return $this->object()->publish();
    }

    /**
     * Delete the object and all its revisions
     *
     * @return ObjectInterface Object
     */
    public function delete()
    {
        return $this->object()->delete();
    }

    /**
     * Undelete the object and all its revisions
     *
     * @return ObjectInterface Object
     */
    public function undelete()
    {
        return $this->object()->undelete();
    }

    /**
     * Add an object relation
     *
     * @param string|RelationInterface $relation Serialized or instantiated object relation
     * @param string|null $relationType Relation type
     * @return ObjectInterface
     */
    public function addRelation($relation, $relationType = null)
    {
        return $this->object()->addRelation($relation, $relationType);
    }

    /**
     * Delete an object relation
     *
     * @param RelationInterface $relation Object relation
     * @return ObjectInterface
     */
    public function deleteRelation(RelationInterface $relation)
    {
        return $this->object()->deleteRelation($relation);
    }

    /**
     * Get all relations (optional: Of a particular type)
     *
     * @param string|null $relationType Optional: Relation type
     * @return array Object relations
     */
    public function getRelations($relationType = null)
    {
        return $this->object()->getRelations($relationType);
    }

    /**
     * Find and return particular relations
     *
     * @param array $criteria Relation criteria
     * @return RelationInterface[] Relations
     */
    public function findRelations(array $criteria)
    {
        return $this->object()->findRelations($criteria);
    }

    /**
     * Return the URL
     *
     * @return RepositoryLocatorInterface|ApparatUrl URL
     */
    abstract public function getUrl();
}
