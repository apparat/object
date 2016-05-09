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

namespace Apparat\Object\Domain\Model\Object;

use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;
use Apparat\Object\Domain\Model\Relation\RelationInterface;

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
     * The constructor is not part of the interface as the object proxy class also implements it
     * with a different signature
     *
     * @param RepositoryPath $path Object repository path
     * @param array $propertyData Property data
     * @param string $payload Object payload
     */
//	public function __construct(RepositoryPath $path, array $propertyData = [], $payload = '');

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
     * Return the object draft mode
     *
     * @return boolean Object draft mode
     */
    public function isDraft();

    /**
     * Return whether the object is in dirty state
     *
     * @return boolean Dirty state
     */
    public function isDirty();

    /**
     * Return whether the object is in mutated state
     *
     * @return boolean Mutated state
     */
    public function isMutated();

    /**
     * Return whether the object is in published state
     *
     * @return boolean Published state
     */
    public function isPublished();

    /**
     * Return the creation date & time
     *
     * @return \DateTimeImmutable Creation date & time
     */
    public function getCreated();

    /**
     * Return the object hash
     *
     * @return string Object hash
     */
    public function getHash();

    /**
     * Return the publication date & time
     *
     * @return \DateTimeImmutable Publication date & time
     */
    public function getPublished();


    /**
     * Return the object title
     *
     * @return string Object title
     */
    public function getTitle();

    /**
     * Set the title
     *
     * @param string $title Title
     * @return ObjectInterface Self reference
     */
    public function setTitle($title);

    /**
     * Return the object slug
     *
     * @return string Object slug
     */
    public function getSlug();

    /**
     * Set the slug
     *
     * @param string $slug Slug
     * @return ObjectInterface Self reference
     */
    public function setSlug($slug);

    /**
     * Return the object description
     *
     * @return string Object description
     */
    public function getDescription();

    /**
     * Set the description
     *
     * @param string $description Description
     * @return ObjectInterface Self reference
     */
    public function setDescription($description);

    /**
     * Return the object abstract
     *
     * @return string Object abstract
     */
    public function getAbstract();

    /**
     * Set the abstract
     *
     * @param string $abstract Abstract
     * @return ObjectInterface Self reference
     */
    public function setAbstract($abstract);


    /**
     * Return the license
     *
     * @return string License
     */
    public function getLicense();

    /**
     * Set the license
     *
     * @param string $license License
     * @return ObjectInterface Self reference
     */
    public function setLicense($license);

    /**
     * Return the privacy
     *
     * @return string Privacy
     */
    public function getPrivacy();

    /**
     * Set the privacy
     *
     * @param string $privacy Privacy
     * @return ObjectInterface Self reference
     */
    public function setPrivacy($privacy);

    /**
     * Return all object keywords
     *
     * @return array Object keywords
     */
    public function getKeywords();

    /**
     * Set the keywords
     *
     * @param array $keywords Keywords
     * @return ObjectInterface Self reference
     */
    public function setKeywords(array $keywords);

    /**
     * Return all object categories
     *
     * @return array Object categories
     */
    public function getCategories();

    /**
     * Set the categories
     *
     * @param array $categories Categories
     * @return ObjectInterface Self reference
     */
    public function setCategories(array $categories);

    /**
     * Return the object repository path
     *
     * @return RepositoryPathInterface Object repository path
     */
    public function getRepositoryPath();

    /**
     * Return the object property data
     *
     * @return array Object property data
     */
    public function getPropertyData();

    /**
     * Return the object payload
     *
     * @return string Object payload
     */
    public function getPayload();

    /**
     * Set the payload
     *
     * @param string $payload Payload
     * @return ObjectInterface Self reference
     */
    public function setPayload($payload);

    /**
     * Return the latitude
     *
     * @return float Latitude
     */
    public function getLatitude();

    /**
     * Set the latitude
     *
     * @param float $latitude Latitude
     * @return ObjectInterface Self reference
     */
    public function setLatitude($latitude);

    /**
     * Return the longitude
     *
     * @return float Longitude
     */
    public function getLongitude();

    /**
     * Set the longitude
     *
     * @param float $longitude Longitude
     * @return ObjectInterface Self reference
     */
    public function setLongitude($longitude);

    /**
     * Return the elevation
     *
     * @return float Elevation
     */
    public function getElevation();

    /**
     * Set the elevation
     *
     * @param float $elevation
     * @return ObjectInterface Self reference
     */
    public function setElevation($elevation);

    /**
     * Return the absolute object URL
     *
     * @return string
     */
    public function getAbsoluteUrl();

    /**
     * Get a domain property value
     *
     * Multi-level properties might be traversed by property name paths separated with colons (":").
     *
     * @param string $property Property name
     * @return mixed Property value
     */
    public function getDomainProperty($property);

    /**
     * Set a domain property value
     *
     * @param string $property Property name
     * @param mixed $value Property value
     * @return ObjectInterface Self reference
     */
    public function setDomainProperty($property, $value);

    /**
     * Get a processing instruction
     *
     * @param string $procInst Processing instruction name
     * @return mixed Processing instruction
     */
    public function getProcessingInstruction($procInst);

    /**
     * Set a processing instruction
     *
     * @param string $procInst Processing instruction name
     * @param mixed $value Processing instruction
     * @return ObjectInterface Self reference
     */
    public function setProcessingInstruction($procInst, $value);

    /**
     * Use a specific object revision
     *
     * @param Revision $revision Revision to be used
     * @return ObjectInterface Object
     */
    public function useRevision(Revision $revision);

    /**
     * Persist the current object revision
     *
     * @return ObjectInterface Object
     */
    public function persist();

    /**
     * Publish the current object revision
     *
     * @return ObjectInterface Object
     */
    public function publish();

    /**
     * Add an object relation
     *
     * @param string $relationType Relation type
     * @param string|RelationInterface $relation Serialized or instantiated object relation
     * @return ObjectInterface
     */
    public function addRelation($relationType, $relation);

    /**
     * Get all relations (optional: Of a particular type)
     *
     * @param string|null $relationType Optional: Relation type
     * @return array Object relations
     */
    public function getRelations($relationType = null);
}
