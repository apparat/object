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
use Apparat\Object\Domain\Model\Object\Traits\IterableTrait;
use Apparat\Object\Domain\Model\Object\Traits\MetaPropertiesTrait;
use Apparat\Object\Domain\Model\Object\Traits\PayloadTrait;
use Apparat\Object\Domain\Model\Object\Traits\ProcessingInstructionsTrait;
use Apparat\Object\Domain\Model\Object\Traits\RelationsTrait;
use Apparat\Object\Domain\Model\Object\Traits\StatesTrait;
use Apparat\Object\Domain\Model\Object\Traits\SystemPropertiesTrait;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;
use Apparat\Object\Domain\Model\Properties\AbstractDomainProperties;
use Apparat\Object\Domain\Model\Properties\InvalidArgumentException as PropertyInvalidArgumentException;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Model\Properties\ProcessingInstructions;
use Apparat\Object\Domain\Model\Properties\Relations;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Domain\Repository\SelectorInterface;
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
        ProcessingInstructionsTrait, PayloadTrait, IterableTrait, StatesTrait;

    /**
     * Repository path
     *
     * @var RepositoryPathInterface
     */
    protected $path;
    /**
     * Latest revision
     *
     * @var Revision
     */
    protected $latestRevision;

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

        // Save the original object path
        $this->path = $path;

        // Load the current revision data
        $this->loadRevisionData($payload, $propertyData);

        // Determine the latest revision number (considering a possible draft)
        $this->latestRevision = $this->hasDraft()
            ? Kernel::create(Revision::class, [$this->getRevision()->getRevision() + 1, true])
            : $this->getRevision();

        // Update the object path
        $this->updatePath();
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

        // Reset the object state
        $this->resetState();
    }

    /**
     * Return whether this object already has a draft revision
     *
     * @return bool Whether this object has a draft revision
     */
    protected function hasDraft()
    {
        // Create the object draft resource path
        $draftPath = $this->path->setRevision(Revision::current(true));

        // Use the object manager to look for a draft resource
        /** @var ManagerInterface $objectManager */
        $objectManager = Kernel::create(Service::class)->getObjectManager();
        return $objectManager->objectResourceExists($draftPath);
    }

    /**
     * Update the object path
     */
    protected function updatePath()
    {
        $revision = $this->getRevision();
        $this->path = $this->path->setRevision(
            !$revision->isDraft() && ($this->getCurrentRevision()->getRevision() == $revision->getRevision())
                ? Revision::current($revision->isDraft())
                : $revision
        )->setHidden($this->isDeleted());
    }

    /**
     * Return this object's current revision
     *
     * @return Revision Current revision
     */
    protected function getCurrentRevision()
    {
        if ($this->latestRevision->isDraft() && ($this->latestRevision->getRevision() > 1)) {
            return Kernel::create(Revision::class, [$this->latestRevision->getRevision() - 1, false]);
        }
        return $this->latestRevision;
    }

    /**
     * Use a specific object revision
     *
     * @param Revision $revision Revision to be used
     * @return ObjectInterface Object
     * @throws OutOfBoundsException If a revision beyond the latest one is requested
     */
    public function useRevision(Revision $revision)
    {
        // If a revision beyond the latest one is requested
        if (!$revision->isCurrent() && ($revision->getRevision() > $this->latestRevision->getRevision())) {
            throw new OutOfBoundsException(
                sprintf('Invalid object revision "%s"', $revision->getRevision()),
                OutOfBoundsException::INVALID_OBJECT_REVISION
            );
        }

        // Determine whether the current revision was requested
        $currentRevision = $this->getCurrentRevision();
        $isCurrentRevision = $revision->isCurrent() || $currentRevision->equals($revision);
        if ($isCurrentRevision) {
            $revision = $currentRevision;
        }

        // If the requested revision is not already used
        if (!$this->getRevision()->equals($revision)) {

            /** @var ManagerInterface $objectManager */
            $objectManager = Kernel::create(Service::class)->getObjectManager();
            /** @var Revision $newRevision */
            $newRevision = $isCurrentRevision ? Revision::current() : $revision;
            /** @var RepositoryPath $newRevisionPath */
            $newRevisionPath = $this->path->setRevision($newRevision);

            // Instantiate the requested revision resource
            $revisionResource = $objectManager->loadObjectResource($newRevisionPath, SelectorInterface::ALL);

            // Load the revision resource data
            $this->loadRevisionData($revisionResource->getPayload(), $revisionResource->getPropertyData());

            // Update the object path
            $this->updatePath();
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
     * @param bool $serialize Serialize property objects
     * @return array Object property data
     */
    public function getPropertyData($serialize = true)
    {
        $propertyData = array_filter([
            SystemProperties::COLLECTION => $this->systemProperties->toArray($serialize),
            MetaProperties::COLLECTION => $this->metaProperties->toArray($serialize),
            AbstractDomainProperties::COLLECTION => $this->domainProperties->toArray($serialize),
            ProcessingInstructions::COLLECTION => $this->processingInstructions->toArray($serialize),
            Relations::COLLECTION => $this->relations->toArray($serialize),
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
     * Return the canonical object URL
     *
     * @return string
     */
    public function getCanonicalUrl()
    {
        $canonicalPath = $this->path->setRevision(Revision::current());
        return getenv('APPARAT_BASE_URL').ltrim($this->path->getRepository()->getUrl(), '/').strval($canonicalPath);
    }

    /**
     * Persist the current object revision
     *
     * @return ObjectInterface Object
     */
    public function persist()
    {
        // If this is not the latest revision
        if ($this->getRevision()->getRevision() !== $this->latestRevision->getRevision()) {
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

        // Reset to a clean state
        $this->resetState();
        $this->latestRevision = $this->getRevision();
        $this->updatePath();

        // Post persistence hook
        $this->postPersist();

        return $this;
    }

    /**
     * Post persistence hook
     *
     * @return void
     */
    protected function postPersist()
    {
    }

    /**
     * Publish the current object revision
     *
     * @return ObjectInterface Object
     */
    public function publish()
    {
        // If this is an unpublished draft
        if ($this->isDraft() & !$this->hasBeenPublished()) {
            $this->setPublishedState();
            $this->latestRevision = $this->latestRevision->setDraft(false);
            $this->updatePath();
        }

        return $this;
    }

    /**
     * Delete the object and all its revisions
     *
     * @return ObjectInterface Object
     */
    public function delete()
    {
        // If this object is not already deleted
        if (!$this->isDeleted() && !$this->hasBeenDeleted()) {
            $this->setDeletedState();
            $this->updatePath();
        }

        return $this;
    }

    /**
     * Undelete the object and all its revisions
     *
     * @return ObjectInterface Object
     */
    public function undelete()
    {
        // If this object is already deleted
        if ($this->isDeleted() && !$this->hasBeenUndeleted()) {
            $this->setUndeletedState();
            $this->updatePath();
        }

        return $this;
    }

    /**
     * Convert this object revision into a draft
     */
    protected function convertToDraft()
    {
        // Assume the latest revision as draft revision
        $draftRevision = $this->latestRevision;

        // If it equals the current revision: Spawn a draft
        if ($draftRevision->equals($this->getCurrentRevision())) {
            $draftRevision = $this->latestRevision = $draftRevision->increment()->setDraft(true);
        }

        // Set the system properties to draft mode
        $this->setSystemProperties($this->systemProperties->createDraft($draftRevision), true);

        // Update the object path
        $this->updatePath();
    }
}
