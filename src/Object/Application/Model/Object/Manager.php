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

namespace Apparat\Object\Application\Model\Object;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Application\Factory\ObjectFactory;
use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\ManagerInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Uri\RepositoryLocator;
use Apparat\Object\Domain\Model\Uri\RepositoryLocatorInterface;
use Apparat\Object\Domain\Repository\InvalidArgumentException as RepositoryInvalidArgumentException;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Domain\Repository\Selector;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Resource\Ports\InvalidReaderArgumentException;

/**
 * Object manager
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class Manager implements ManagerInterface
{
    /**
     * Create and return a new object
     *
     * @param RepositoryInterface $repository Repository to create the object in
     * @param Type $type Object type
     * @param string $payload Object payload
     * @param array $propertyData Object property data
     * @param \DateTimeInterface $creationDate Object creation date
     * @return ObjectInterface Object
     */
    public function createObject(
        RepositoryInterface $repository,
        Type $type,
        $payload = '',
        array $propertyData = [],
        \DateTimeInterface $creationDate = null
    ) {
        // Set the creation date to now if empty
        if ($creationDate === null) {
            $creationDate = new \DateTimeImmutable('now');
        }

        // Construct a creation closure
        $creationClosure = function (Id $uid) use ($repository, $type, $payload, $propertyData, $creationDate) {
            /** @var Revision $revision */
            $revision = Kernel::create(Revision::class, [1, true]);

            /** @var RepositoryLocator $repositoryLocator */
            $repositoryLocator = Kernel::create(RepositoryLocator::class, [$repository]);
            $repositoryLocator = $repositoryLocator->setId($uid);
            $repositoryLocator = $repositoryLocator->setRevision($revision);
            $repositoryLocator = $repositoryLocator->setObjectType($type);
            $repositoryLocator = $repositoryLocator->setCreationDate($creationDate);

            return ObjectFactory::createFromParams($repositoryLocator, $payload, $propertyData);
        };

        // Wrap the object creation in an ID allocation transaction
        return $repository->getAdapterStrategy()->createObjectResource($creationClosure);
    }

    /**
     * Load an object from a repository
     *
     * @param RepositoryLocatorInterface $locator Repository object locator
     * @param int $visibility Object visibility
     * @return ObjectInterface Object
     */
    public function loadObject(RepositoryLocatorInterface $locator, $visibility = SelectorInterface::ALL)
    {
        // Create the current revision locator
        /** @var RepositoryLocatorInterface $currentLocator */
        $currentLocator = $locator->setRevision(Revision::current());

        // Load the object resource respecting visibility constraints
        $objectResource = $this->loadObjectResource($currentLocator, $visibility);

        // Instantiate the object
        $object = ObjectFactory::createFromResource($currentLocator, $objectResource);

        // Use and return the requested object revision
        return $object->useRevision($locator->getRevision());
    }

    /**
     * Load and return an object resource respecting visibility constraints
     *
     * @param RepositoryLocatorInterface $currentLocator
     * @param int $visibility Object visibility
     * @return ResourceInterface Object resource
     * @throws InvalidArgumentException If the resource could not be loaded
     */
    public function loadObjectResource(
        RepositoryLocatorInterface &$currentLocator,
        $visibility = SelectorInterface::ALL
    ) {
        // Validate the object visibility
        $this->validateVisibility($visibility);

        $objectResource = null;

        // Create the current revision locators (visible and hidden)
        /** @var RepositoryLocatorInterface[] $currentLocators */
        $currentLocators = array_filter([
            ($visibility & SelectorInterface::VISIBLE) ? $currentLocator->setHidden(false) : null,
            ($visibility & SelectorInterface::HIDDEN) ? $currentLocator->setHidden(true) : null,
        ]);

        // Run through the possible revision locators
        foreach ($currentLocators as $currentLocatorIndex => $currentLocator) {
            try {
                // Load the current object resource
                $objectResource = $this->getObjectResource($currentLocator);
                break;

                // In case of an error
            } catch (InvalidReaderArgumentException $e) {
                // If it's not an error about the resource not existing or if it's the last possible option
                if (($e->getCode() != InvalidReaderArgumentException::RESOURCE_DOES_NOT_EXIST)
                    || ($currentLocatorIndex >= (count($currentLocators) - 1))
                ) {
                    throw $e;
                }
            }
        }

        // If the resource could not be loaded
        if (!($objectResource instanceof ResourceInterface)) {
            throw new InvalidArgumentException(
                'Resource could not be loaded',
                InvalidArgumentException::RESOURCE_NOT_LOADED
            );
        }

        return $objectResource;
    }

    /**
     * Validate a given object visibility
     *
     * @param int $visibility Object visibility
     * @throw RepositoryInvalidArgumentException If the visibility requirement is invalid
     */
    protected function validateVisibility($visibility)
    {
        // If the visibility requirement is invalid
        if (!Selector::isValidVisibility($visibility)) {
            throw new RepositoryInvalidArgumentException(
                sprintf(
                    'Invalid repository selector visibility "%s"',
                    $visibility
                ),
                RepositoryInvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT,
                null,
                'visibility'
            );
        }
    }

    /**
     * Instantiate object resource
     *
     * @param RepositoryLocatorInterface $locator
     * @return ResourceInterface Object resource
     */
    public function getObjectResource(RepositoryLocatorInterface $locator)
    {
        return $locator->getRepository()->getAdapterStrategy()->getObjectResource(
            $locator->withExtension(getenv('OBJECT_RESOURCE_EXTENSION'))
        );
    }

    /**
     * Test whether an object resource exists
     *
     * @param RepositoryLocatorInterface $locator
     * @return boolean Object resource exists
     */
    public function objectResourceExists(RepositoryLocatorInterface $locator)
    {
        return $locator->getRepository()->getAdapterStrategy()->hasResource(
            $locator->withExtension(getenv('OBJECT_RESOURCE_EXTENSION'))
        );
    }
}
