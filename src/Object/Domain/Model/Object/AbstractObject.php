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

namespace Apparat\Object\Domain\Model\Object;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Author\AuthorInterface;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;
use Apparat\Object\Domain\Model\Properties\AbstractDomainProperties;
use Apparat\Object\Domain\Model\Properties\InvalidArgumentException as PropertyInvalidArgumentException;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Model\Properties\ProcessingInstructions;
use Apparat\Object\Domain\Model\Properties\Relations;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Domain\Repository\Service;

/**
 * Abstract object
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
abstract class AbstractObject implements ObjectInterface
{
    /**
     * Clean state
     *
     * @var int
     */
    const STATE_CLEAN = 0;
    /**
     * Dirty state
     *
     * @var int
     */
    const STATE_DIRTY = 1;
    /**
     * Mutated state
     *
     * @var int
     */
    const STATE_MUTATED = 2;
    /**
     * System properties
     *
     * @var SystemProperties
     */
    protected $systemProperties;
    /**
     * Meta properties
     *
     * @var MetaProperties
     */
    protected $metaProperties;
    /**
     * Domain properties
     *
     * @var AbstractDomainProperties
     */
    protected $domainProperties;
    /**
     * Object payload
     *
     * @var string
     */
    protected $payload;
    /**
     * Repository path
     *
     * @var RepositoryPathInterface
     */
    protected $path;
    /**
     * Domain property collection class
     *
     * @var string
     */
    protected $domainPropertyCClass = AbstractDomainProperties::class;
    /**
     * Object relations
     *
     * @var Relations
     */
    protected $relations;
    /**
     * Processing instructions
     *
     * @var ProcessingInstructions
     */
    protected $processingInstructions;
    /**
     * Latest revision index
     *
     * @var Revision
     */
    protected $latestRevision;
    /**
     * Object state
     *
     * @var int
     */
    protected $state = self::STATE_CLEAN;
    /**
     * Property collection states
     *
     * @var array
     */
    protected $collectionStates = [];

    /**
     * Object constructor
     *
     * @param string $payload Object payload
     * @param array $propertyData Property data
     * @param RepositoryPathInterface $path Object repository path
     */
    public function __construct($payload = '', array $propertyData = [], RepositoryPathInterface $path = null)
    {
        // If the domain property collection class is invalid
        if (!$this->domainPropertyCClass
            || !class_exists($this->domainPropertyCClass)
            || !(new \ReflectionClass($this->domainPropertyCClass))->isSubclassOf(AbstractDomainProperties::class)
        ) {
            throw new PropertyInvalidArgumentException(
                sprintf(
                    'Invalid domain property collection class "%s"',
                    $this->domainPropertyCClass
                ),
                PropertyInvalidArgumentException::INVALID_DOMAIN_PROPERTY_COLLECTION_CLASS
            );
        }

        $this->path = $path;

        // Load the current revision data
        $this->loadRevisionData($payload, $propertyData);

        // Save the latest revision index
        $this->latestRevision = $this->getRevision();
    }

    /**
     * Load object revision data
     *
     * @param string $payload Object payload
     * @param array $propertyData Property data
     */
    protected function loadRevisionData($payload = '', array $propertyData = [])
    {
        $this->payload = $payload;

        // Instantiate the system properties
        $systemPropertyData = (empty($propertyData[SystemProperties::COLLECTION]) ||
            !is_array(
                $propertyData[SystemProperties::COLLECTION]
            )) ? [] : $propertyData[SystemProperties::COLLECTION];
        $this->systemProperties = Kernel::create(SystemProperties::class, [$systemPropertyData, $this]);

        // Instantiate the meta properties
        $metaPropertyData = (empty($propertyData[MetaProperties::COLLECTION]) ||
            !is_array(
                $propertyData[MetaProperties::COLLECTION]
            )) ? [] : $propertyData[MetaProperties::COLLECTION];
        /** @var MetaProperties $metaPropertyCollection */
        $metaPropertyCollection = Kernel::create(MetaProperties::class, [$metaPropertyData, $this]);
        $this->setMetaProperties($metaPropertyCollection, true);

        // Instantiate the domain properties
        $domainPropertyData = (empty($propertyData[AbstractDomainProperties::COLLECTION]) ||
            !is_array(
                $propertyData[AbstractDomainProperties::COLLECTION]
            )) ? [] : $propertyData[AbstractDomainProperties::COLLECTION];
        $this->domainProperties = Kernel::create($this->domainPropertyCClass, [$domainPropertyData, $this]);

        // Instantiate the processing instructions
        $procInstData = (empty($propertyData[ProcessingInstructions::COLLECTION]) ||
            !is_array(
                $propertyData[ProcessingInstructions::COLLECTION]
            )) ? [] : $propertyData[ProcessingInstructions::COLLECTION];
        $this->processingInstructions = Kernel::create(ProcessingInstructions::class, [$procInstData, $this]);

        // Instantiate the object relations
        $relationData = (empty($propertyData[Relations::COLLECTION]) ||
            !is_array(
                $propertyData[Relations::COLLECTION]
            )) ? [] : $propertyData[Relations::COLLECTION];
        $this->relations = Kernel::create(Relations::class, [$relationData, $this]);

        // Reset the object state to clean
        $this->state = self::STATE_CLEAN;
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
        if (!$overwrite && !empty($this->collectionStates[MetaProperties::COLLECTION]) &&
            ($metaPropertiesState !== $this->collectionStates[MetaProperties::COLLECTION])
        ) {
            // Flag this object as mutated
            $this->setMutatedState();
        }

        $this->collectionStates[MetaProperties::COLLECTION] = $metaPropertiesState;
    }

    /**
     * Set the object state to mutated
     */
    protected function setMutatedState()
    {
        // If this object is not in mutated state yet
        if (!($this->state & self::STATE_MUTATED)) {
            // TODO: Take actions to make this the most recent revision (in draft mode)
        }

        // Enable the mutated (and dirty) state
        $this->state |= (self::STATE_DIRTY | self::STATE_MUTATED);
    }

    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision()
    {
        return $this->systemProperties->getRevision();
    }

    /**
     * Return whether the object is in dirty state
     *
     * @return boolean Dirty state
     */
    public function isDirty()
    {
        return !!($this->state & self::STATE_DIRTY);
    }

    /**
     * Return whether the object is in mutated state
     *
     * @return boolean Mutated state
     */
    public function isMutated()
    {
        return !!($this->state & self::STATE_MUTATED);
    }

    /**
     * Return the object draft mode
     *
     * @return boolean Object draft mode
     */
    public function isDraft()
    {
        return $this->systemProperties->isDraft();
    }

    /**
     * Use a specific object revision
     *
     * @param Revision $revision Revision to be used
     * @return ObjectInterface Object
     * @throws OutOfBoundsException If the requested revision is invalid
     */
    public function useRevision(Revision $revision)
    {
        $isCurrentRevision = false;

        // If the requested revision is invalid
        if (!$revision->isCurrent() &&
            (($revision->getRevision() < 1) || ($revision->getRevision() > $this->latestRevision->getRevision()))
        ) {
            throw new OutOfBoundsException(sprintf('Invalid object revision "%s"', $revision->getRevision()),
                OutOfBoundsException::INVALID_OBJECT_REVISION);
        }

        // If the current revision got requested
        if ($revision->isCurrent()) {
            $isCurrentRevision = true;
            $revision = $this->latestRevision;
        }

        // If the requested revision is not already used
        if ($revision != $this->getRevision()) {
            /** @var ManagerInterface $objectManager */
            $objectManager = Kernel::create(Service::class)->getObjectManager();

            // Load the requested object revision resource
            /** @var Revision $newRevision */
            $newRevision = $isCurrentRevision ? Kernel::create(Revision::class, [Revision::CURRENT]) :
                $revision;
            /** @var RepositoryPath $newRevisionPath */
            $newRevisionPath = $this->path->setRevision($newRevision);
            $revisionResource = $objectManager->loadObject($newRevisionPath);

            // Load the revision resource data
            $this->loadRevisionData($revisionResource->getPayload(), $revisionResource->getPropertyData());

            // Set the current revision path
            $this->path = $newRevisionPath;
        }

        return $this;
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->systemProperties->getId();
    }

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getType()
    {
        return $this->systemProperties->getType();
    }

    /**
     * Return the creation date & time
     *
     * @return \DateTimeImmutable Creation date & time
     */
    public function getCreated()
    {
        return $this->systemProperties->getCreated();
    }

    /**
     * Return the publication date & time
     *
     * @return \DateTimeImmutable Publication date & time
     */
    public function getPublished()
    {
        return $this->systemProperties->getPublished();
    }

    /**
     * Return the object hash
     *
     * @return string Object hash
     */
    public function getHash()
    {
        return $this->systemProperties->getHash();
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
     * Return all object authors
     *
     * @return AuthorInterface[] Authors
     */
    public function getAuthors()
    {
        return $this->metaProperties->getAuthors();
    }

    /**
     * Add an object author
     *
     * @param AuthorInterface $author Author
     * @return ObjectInterface Self reference
     */
    public function addAuthor(AuthorInterface $author)
    {
        $authors = $this->metaProperties->getAuthors();
        $authors[] = $author;
        $this->metaProperties->setAuthors($authors);
        return $this;
    }

    /**
     * Return the object repository path
     *
     * @return RepositoryPathInterface Object repository path
     */
    public function getRepositoryPath()
    {
        return $this->path;
    }

    /**
     * Return the object property data
     *
     * @return array Object property data
     */
    public function getPropertyData()
    {
        $propertyData = array_filter([
            SystemProperties::COLLECTION => $this->systemProperties->toArray(),
            MetaProperties::COLLECTION => $this->metaProperties->toArray(),
            AbstractDomainProperties::COLLECTION => $this->domainProperties->toArray(),
            ProcessingInstructions::COLLECTION => $this->processingInstructions->toArray(),
            Relations::COLLECTION => $this->relations->toArray(),
        ], function (array $collection) {
            return (boolean)count($collection);
        });

        return $propertyData;
    }

    /**
     * Return the object payload
     *
     * @return string Object payload
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Return the absolute object URL
     *
     * @return string
     */
    public function getAbsoluteUrl()
    {
        return getenv('APPARAT_BASE_URL').ltrim($this->path->getRepository()->getUrl(), '/').strval($this->path);
    }

    /**
     * Get a particular property value
     *
     * Multi-level properties might be traversed by property name paths separated with colons (":").
     *
     * @param string $property Property name
     * @return mixed Property value
     */
    public function getDomainProperty($property)
    {
        return $this->domainProperties->getProperty($property);
    }

    protected function setDirtyState()
    {

        // If this object is not in dirty state yet
        if (!($this->state & self::STATE_DIRTY)) {

        }

        // Enable the dirty state
        $this->state |= self::STATE_DIRTY;
    }
}
