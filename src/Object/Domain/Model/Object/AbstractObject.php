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
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;
use Apparat\Object\Domain\Model\Properties\AbstractDomainProperties;
use Apparat\Object\Domain\Model\Properties\GenericPropertiesInterface;
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
     * Published state
     *
     * @var int
     */
    const STATE_PUBLISHED = 4;
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

        // Right after instantiation it's always the current revision
        $this->path = $path->setRevision(Revision::current());

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
        /** @var AbstractDomainProperties $domainPropertyCollection */
        $domainPropertyCollection = Kernel::create($this->domainPropertyCClass, [$domainPropertyData, $this]);
        $this->setDomainProperties($domainPropertyCollection, true);

        // Instantiate the processing instructions
        $procInstData = (empty($propertyData[ProcessingInstructions::COLLECTION]) ||
            !is_array(
                $propertyData[ProcessingInstructions::COLLECTION]
            )) ? [] : $propertyData[ProcessingInstructions::COLLECTION];
        /** @var ProcessingInstructions $procInstCollection */
        $procInstCollection = Kernel::create(ProcessingInstructions::class, [$procInstData, $this]);
        $this->setProcessingInstructions($procInstCollection, true);

        // Instantiate the object relations
        $relationData = (empty($propertyData[Relations::COLLECTION]) ||
            !is_array(
                $propertyData[Relations::COLLECTION]
            )) ? [] : $propertyData[Relations::COLLECTION];
        /** @var Relations $relationCollection */
        $relationCollection = Kernel::create(Relations::class, [$relationData, $this]);
        $this->setRelations($relationCollection, true);

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
     * Set the object state to mutated
     */
    protected function setMutatedState()
    {
        // If this object is not in mutated state yet
        if (!($this->state & self::STATE_MUTATED) && !$this->isDraft()) {
            // TODO: Send signal
            $this->convertToDraft();
        }

        // Enable the mutated (and dirty) state
        $this->state |= (self::STATE_DIRTY | self::STATE_MUTATED);
    }

    /**
     * Return the object draft mode
     *
     * @return boolean Object draft mode
     */
    public function isDraft()
    {
        return $this->systemProperties->isDraft() || $this->isPublished();
    }

    /**
     * Return whether the object is in published state
     *
     * @return boolean Published state
     */
    public function isPublished()
    {
        return !!($this->state & self::STATE_PUBLISHED);
    }

    /**
     * Convert this object revision into a draft
     */
    protected function convertToDraft()
    {
        // Increment the latest revision number
        $this->latestRevision = $this->latestRevision->increment();

        // Create draft system properties
        $this->systemProperties = $this->systemProperties->createDraft($this->latestRevision);

        // Adapt the system properties collection state
        $this->collectionStates[SystemProperties::COLLECTION] = spl_object_hash($this->systemProperties);

        // Set the draft flag on the repository path
        $this->path = $this->path->setDraft(true)->setRevision(Revision::current());

        // If this is not already a draft ...
        // Recreate the system properties
        // Copy the object ID
        // Copy the object type
        // Set the revision number to latest revision + 1
        // Set the creation date to now
        // Set no publication date
        // Set the draft flag on the repository path
        // Increase the latest revision by 1

        // Else if this is a draft
        // No action needed
    }

    /**
     * Set the domain properties collection
     *
     * @param GenericPropertiesInterface $domainProperties Domain property collection
     * @param bool $overwrite Overwrite the existing collection (if present)
     */
    protected function setDomainProperties(GenericPropertiesInterface $domainProperties, $overwrite = false)
    {
        $this->domainProperties = $domainProperties;
        $domainPropertiesState = spl_object_hash($this->domainProperties);

        // If the domain property collection state has changed
        if (!$overwrite
            && !empty($this->collectionStates[AbstractDomainProperties::COLLECTION])
            && ($domainPropertiesState !== $this->collectionStates[AbstractDomainProperties::COLLECTION])
        ) {
            // Flag this object as mutated
            $this->setMutatedState();
        }

        $this->collectionStates[AbstractDomainProperties::COLLECTION] = $domainPropertiesState;
    }

    /**
     * Set the processing instruction collection
     *
     * @param GenericPropertiesInterface $processingInstructions Processing instruction collection
     * @param bool $overwrite Overwrite the existing collection (if present)
     */
    protected function setProcessingInstructions(GenericPropertiesInterface $processingInstructions, $overwrite = false)
    {
        $this->processingInstructions = $processingInstructions;
        $processingInstructionsState = spl_object_hash($this->processingInstructions);

        // If the domain property collection state has changed
        if (!$overwrite
            && !empty($this->collectionStates[ProcessingInstructions::COLLECTION])
            && ($processingInstructionsState !== $this->collectionStates[ProcessingInstructions::COLLECTION])
        ) {
            // Flag this object as dirty
            $this->setDirtyState();
        }

        $this->collectionStates[ProcessingInstructions::COLLECTION] = $processingInstructionsState;
    }

    /**
     * Set the object state to dirty
     */
    protected function setDirtyState()
    {
        // If this object is not in dirty state yet
        if (!($this->state & self::STATE_DIRTY)) {
            // TODO: Send signal
        }

        // Enable the dirty state
        $this->state |= self::STATE_DIRTY;
    }

    /**
     * Set the relations collection
     *
     * @param Relations $relations Relations collection
     * @param bool $overwrite Overwrite the existing collection (if present)
     */
    protected function setRelations(Relations $relations, $overwrite = false)
    {
        $this->relations = $relations;
        $relationsState = spl_object_hash($this->relations);

        // If the domain property collection state has changed
        if (!$overwrite
            && !empty($this->collectionStates[Relations::COLLECTION])
            && ($relationsState !== $this->collectionStates[Relations::COLLECTION])
        ) {
            // Flag this object as dirty
            $this->setDirtyState();
        }

        $this->collectionStates[Relations::COLLECTION] = $relationsState;
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
     * Return whether the object is in mutated state
     *
     * @return boolean Mutated state
     */
    public function isMutated()
    {
        return !!($this->state & self::STATE_MUTATED);
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
            $newRevision = $isCurrentRevision ? Revision::current() : $revision;
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
     * @return \DateTimeImmutable|null Publication date & time
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
     * Set the payload
     *
     * @param string $payload Payload
     * @return ObjectInterface Self reference
     */
    public function setPayload($payload)
    {
        // If the payload is changed
        if ($payload !== $this->payload) {
            $this->setMutatedState();
        }

        $this->payload = $payload;
        return $this;
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
     * Get a domain property value
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

    /**
     * Set a domain property value
     *
     * @param string $property Property name
     * @param mixed $value Property value
     * @return ObjectInterface Self reference
     */
    public function setDomainProperty($property, $value)
    {
        $this->setDomainProperties($this->domainProperties->setProperty($property, $value));
        return $this;
    }

    /**
     * Get a processing instruction
     *
     * @param string $procInst Processing instruction name
     * @return mixed Processing instruction
     */
    public function getProcessingInstruction($procInst)
    {
        return $this->processingInstructions->getProperty($procInst);
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
        $this->setProcessingInstructions($this->processingInstructions->setProperty($procInst, $value));
        return $this;
    }

    /**
     * Persist the current object revision
     *
     * @return ObjectInterface Object
     */
    public function persist()
    {
        // If this is not the latest revision
        if ($this->getRevision() != $this->latestRevision) {
            throw new RuntimeException(
                sprintf(
                    'Cannot persist revision %s/%s',
                    $this->getRevision()->getRevision(),
                    $this->latestRevision->getRevision()
                ),
                RuntimeException::CANNOT_PERSIST_EARLIER_REVISION
            );
        }

        // Update the object repository
        $this->path->getRepository()->updateObject($this);

        // Reset state
        $this->state = self::STATE_CLEAN;

        return $this;
    }

    /**
     * Publish the current object revision
     *
     * @return ObjectInterface Object
     */
    public function publish()
    {
        // If this is a draft
        if ($this->isDraft()) {
            // TODO: Send signal

            // Create draft system properties
            $this->systemProperties = $this->systemProperties->publish();

            // Adapt the system properties collection state
            $this->collectionStates[SystemProperties::COLLECTION] = spl_object_hash($this->systemProperties);

            // Set the draft flag on the repository path
            $this->path = $this->path->setDraft(false);

            // Flag this object as dirty
            $this->setPublishedState();
        }

        return $this;
    }

    /**
     * Set the object state to published
     */
    protected function setPublishedState()
    {
        // If this object is not in dirty state yet
        if (!($this->state & self::STATE_PUBLISHED)) {
            // TODO: Send signal
        }

        // Enable the dirty state
        $this->state |= (self::STATE_DIRTY | self::STATE_PUBLISHED);
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
}
