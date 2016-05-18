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
use Apparat\Object\Domain\Model\Object\Traits\DomainPropertiesTrait;
use Apparat\Object\Domain\Model\Object\Traits\MetaPropertiesTrait;
use Apparat\Object\Domain\Model\Object\Traits\PayloadTrait;
use Apparat\Object\Domain\Model\Object\Traits\ProcessingInstructionsTrait;
use Apparat\Object\Domain\Model\Object\Traits\RelationsTrait;
use Apparat\Object\Domain\Model\Object\Traits\SystemPropertiesTrait;
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
     * Use traits
     */
    use SystemPropertiesTrait, MetaPropertiesTrait, DomainPropertiesTrait, RelationsTrait,
        ProcessingInstructionsTrait, PayloadTrait;
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
     * Deleted state
     *
     * @var int
     */
    const STATE_DELETED = 8;
    /**
     * Repository path
     *
     * @var RepositoryPathInterface
     */
    protected $path;
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
     * @param RepositoryPathInterface $path Object repository path
     * @param string $payload Object payload
     * @param array $propertyData Property data
     */
    public function __construct(RepositoryPathInterface $path, $payload = '', array $propertyData = [])
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
        /** @var MetaProperties $metaProperties */
        $metaProperties = Kernel::create(MetaProperties::class, [$metaPropertyData, $this]);
        $this->setMetaProperties($metaProperties, true);

        // Instantiate the domain properties
        $domainPropertyData = (empty($propertyData[AbstractDomainProperties::COLLECTION]) ||
            !is_array(
                $propertyData[AbstractDomainProperties::COLLECTION]
            )) ? [] : $propertyData[AbstractDomainProperties::COLLECTION];
        /** @var AbstractDomainProperties $domainProperties */
        $domainProperties = Kernel::create($this->domainPropertyCClass, [$domainPropertyData, $this]);
        $this->setDomainProperties($domainProperties, true);

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
            throw new OutOfBoundsException(
                sprintf('Invalid object revision "%s"', $revision->getRevision()),
                OutOfBoundsException::INVALID_OBJECT_REVISION
            );
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
     * Return the absolute object URL
     *
     * @return string
     */
    public function getAbsoluteUrl()
    {
        return getenv('APPARAT_BASE_URL').ltrim($this->path->getRepository()->getUrl(), '/').strval($this->path);
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
     * Delete the object and all its revisions
     *
     * @return ObjectInterface Object
     */
    public function delete()
    {
        // TODO: Implement delete() method.
        $this->setDeletionState(true);
        return $this;
    }

    /**
     * Undelete the object and all its revisions
     *
     * @return ObjectInterface Object
     */
    public function undelete()
    {
        // TODO: Implement undelete() method.
        $this->setDeletionState(false);
        return $this;
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
     * Set the object state to mutated
     */
    protected function setMutatedState()
    {
        // If this object is not in mutated state yet
        if (!($this->state & self::STATE_MUTATED) && !$this->isDraft()) {
            // TODO: Send signal
            $this->convertToDraft();
        }

        // Enable the mutated state
        $this->state |= self::STATE_MUTATED;

        // Enable the dirty state
        $this->setDirtyState();
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

        // Update the modification timestamp
        $this->setSystemProperties($this->systemProperties->touch(), true);
    }

    /**
     * Set the object deletion state
     *
     * @param boolean $deleted Deletion state
     */
    protected function setDeletionState($deleted)
    {
        $deleted = boolval($deleted);

        // If this object is not in the requested deltion state yet
        if ($deleted != boolval($this->state & self::STATE_DELETED)) {
            // TODO: Send signal
        }

        // If the object should be deleted
        if ($deleted) {
            $this->state |= self::STATE_DELETED;

            // Set the deletion timestamp
            $this->setSystemProperties($this->systemProperties->delete(), true);
            return;
        }

        // Undelete the object
        $this->state &= ~self::STATE_DELETED;

        // Unset the deletion timestamp
        $this->setSystemProperties($this->systemProperties->undelete(), true);
    }
}
