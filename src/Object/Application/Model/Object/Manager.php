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
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;
use Apparat\Object\Domain\Repository\InvalidArgumentException;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Domain\Repository\Selector;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Resource\Ports\InvalidReaderArgumentException;

/**
 * Object manager
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
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
     * @return ObjectInterface Object
     */
    public function createObject(RepositoryInterface $repository, Type $type, $payload = '', array $propertyData = [])
    {
        // Construct a creation closure
        $creationClosure = function (Id $uid) use ($repository, $type, $payload, $propertyData) {
            /** @var Revision $revision */
            $revision = Kernel::create(Revision::class, [1, true]);

            /** @var RepositoryPath $repositoryPath */
            $repositoryPath = Kernel::create(RepositoryPath::class, [$repository]);
            $repositoryPath = $repositoryPath->setId($uid);
            $repositoryPath = $repositoryPath->setRevision($revision);
            $repositoryPath = $repositoryPath->setType($type);
            $repositoryPath = $repositoryPath->setCreationDate(new \DateTimeImmutable());

            return ObjectFactory::createFromParams($repositoryPath, $payload, $propertyData);
        };

        // Wrap the object creation in an ID allocation transaction
        return $repository->getAdapterStrategy()->createObjectResource($creationClosure);
    }

    /**
     * Load an object from a repository
     *
     * @param RepositoryPathInterface $path Repository object path
     * @param int $visibility Object visibility
     * @return ObjectInterface Object
     * @throws InvalidArgumentException If the visibility requirement is invalid
     */
    public function loadObject(RepositoryPathInterface $path, $visibility = SelectorInterface::ALL)
    {
        // Create the current revision path
        /** @var RepositoryPathInterface $currentPath */
        $currentPath = $path->setRevision(Revision::current());

        // Load the object resource respecting visibility constraints
        $objectResource = $this->loadObjectResource($currentPath, $visibility);

        // Instantiate the object
        $object = ObjectFactory::createFromResource($currentPath, $objectResource);

        // Use and return the requested object revision
        return $object->useRevision($path->getRevision());
    }

    /**
     * Load and return an object resource respecting visibility constraints
     *
     * @param RepositoryPathInterface $currentPath
     * @param int $visibility Object visibility
     * @return ResourceInterface Object resource
     */
    public function loadObjectResource(RepositoryPathInterface &$currentPath, $visibility = SelectorInterface::ALL)
    {
        // Validate the object visibility
        $this->validateVisibility($visibility);

        $objectResource = null;

        // Create the current revision paths (visible and hidden)
        /** @var RepositoryPathInterface[] $currentPaths */
        $currentPaths = array_filter([
            ($visibility & SelectorInterface::VISIBLE) ? $currentPath->setHidden(false) : null,
            ($visibility & SelectorInterface::HIDDEN) ? $currentPath->setHidden(true) : null,
        ]);

        // Run through the possible revision paths
        foreach ($currentPaths as $currentPathIndex => $currentPath) {
            try {
                // Load the current object resource
                $objectResource = $this->getObjectResource($currentPath);
                break;

                // In case of an error
            } catch (InvalidReaderArgumentException $e) {
                // If it's not an error about the resource not existing or if it's the last possible option
                if (($e->getCode() != InvalidReaderArgumentException::RESOURCE_DOES_NOT_EXIST)
                    || ($currentPathIndex >= (count($currentPaths) - 1))
                ) {
                    throw $e;
                }
            }
        }

        return $objectResource;
    }

    /**
     * Validate a given object visibility
     *
     * @param int $visibility Object visibility
     * @throw InvalidArgumentException If the visibility requirement is invalid
     */
    protected function validateVisibility($visibility) {
        // If the visibility requirement is invalid
        if (!Selector::isValidVisibility($visibility)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid repository selector visibility "%s"',
                    $visibility
                ),
                InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT,
                null,
                'visibility'
            );
        }
    }

    /**
     * Instantiate object resource
     *
     * @param RepositoryPathInterface $path
     * @return ResourceInterface Object resource
     */
    public function getObjectResource(RepositoryPathInterface $path)
    {
        return $path->getRepository()->getAdapterStrategy()->getObjectResource(
            $path->withExtension(getenv('OBJECT_RESOURCE_EXTENSION'))
        );
    }

    /**
     * Test whether an object resource exists
     *
     * @param RepositoryPathInterface $path
     * @return boolean Object resource exists
     */
    public function objectResourceExists(RepositoryPathInterface $path)
    {
        return $path->getRepository()->getAdapterStrategy()->hasResource(
            $path->withExtension(getenv('OBJECT_RESOURCE_EXTENSION'))
        );
    }
}
