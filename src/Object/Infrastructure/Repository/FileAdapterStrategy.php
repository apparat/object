<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Infrastructure
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

namespace Apparat\Object\Infrastructure\Repository;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Application\Repository\AbstractAdapterStrategy;
use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Uri\LocatorInterface;
use Apparat\Object\Domain\Model\Uri\RepositoryLocator;
use Apparat\Object\Domain\Model\Uri\RepositoryLocatorInterface;
use Apparat\Object\Domain\Repository\AdapterStrategyInterface;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Domain\Repository\RuntimeException as DomainRepositoryRuntimeException;
use Apparat\Object\Domain\Repository\Selector;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Object\Infrastructure\Factory\ResourceFactory;
use Apparat\Object\Infrastructure\Utilities\File;
use Apparat\Resource\Domain\Model\Resource\AbstractResource;
use Apparat\Resource\Infrastructure\Io\File\AbstractFileReaderWriter;
use Apparat\Resource\Infrastructure\Io\File\Writer;

/**
 * File adapter strategy
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 */
class FileAdapterStrategy extends AbstractAdapterStrategy
{
    /**
     * Adapter strategy type
     *
     * @var string
     */
    const TYPE = 'file';
    /**
     * Glob visibilities
     *
     * @var array
     */
    protected static $globVisibilities = [
        SelectorInterface::VISIBLE => '',
        SelectorInterface::HIDDEN => '.',
        SelectorInterface::ALL => '{.,}',
    ];
    /**
     * Configuration
     *
     * @var array
     */
    protected $config = null;
    /**
     * Root directory (without trailing directory separator)
     *
     * @var string
     */
    protected $root = null;
    /**
     * Configuration directory (including trailing directory separator)
     *
     * @var string
     */
    protected $configDir = null;

    /**
     * Adapter strategy constructor
     *
     * @param array $config Adapter strategy configuration
     * @throws InvalidArgumentException If the root directory configuration is empty
     * @throws InvalidArgumentException If the root directory configuration is invalid
     */
    public function __construct(array $config)
    {
        parent::__construct($config, ['root']);

        // If the root directory configuration is empty
        if (empty($this->config['root'])) {
            throw new InvalidArgumentException(
                'Empty file adapter strategy root',
                InvalidArgumentException::EMTPY_FILE_STRATEGY_ROOT
            );
        }

        // Get the real locator of the root directory
        $this->root = realpath($this->config['root']);

        // If the repository should be initialized
        if (!empty($this->config['init'])
            && (boolean)$this->config['init']
            && $this->initializeRepository()
        ) {
            $this->root = realpath($this->config['root']);
        }

        // If the root directory configuration is still invalid
        if (empty($this->root) || !@is_dir($this->root)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid file adapter strategy root "%s"',
                    $this->config['root']
                ),
                InvalidArgumentException::INVALID_FILE_STRATEGY_ROOT
            );
        }

        $this->configDir = $this->root.DIRECTORY_SEPARATOR.'.repo'.DIRECTORY_SEPARATOR;
    }

    /**
     * Initialize the repository
     *
     * @return boolean Success
     * @throws DomainRepositoryRuntimeException If the repository cannot be initialized
     * @throws DomainRepositoryRuntimeException If the repository size descriptor can not be created
     */
    public function initializeRepository()
    {
        // Successively create the repository directories
        $repoDirectories = [$this->config['root'], $this->config['root'].DIRECTORY_SEPARATOR.'.repo'];
        foreach ($repoDirectories as $repoDirectory) {
            // If the repository cannot be initialized
            if (file_exists($repoDirectory) ? !is_dir($repoDirectory) : !mkdir($repoDirectory, 0777, true)) {
                throw new DomainRepositoryRuntimeException(
                    'Could not initialize repository',
                    DomainRepositoryRuntimeException::REPO_NOT_INITIALIZED
                );
            }
        }

        // If the repository size descriptor can not be created
        $configDir = $this->config['root'].DIRECTORY_SEPARATOR.'.repo'.DIRECTORY_SEPARATOR;
        if ((file_exists($configDir.'size.txt') && !is_file($configDir.'size.txt'))
            || !file_put_contents($configDir.'size.txt', '0')
        ) {
            throw new DomainRepositoryRuntimeException(
                'Could not create repository size descriptor',
                DomainRepositoryRuntimeException::REPO_SIZE_DESCRIPTOR_NOT_CREATED
            );
        }

        return true;
    }

    /**
     * Find objects by selector
     *
     * @param Selector|SelectorInterface $selector Object selector
     * @param RepositoryInterface $repository Object repository
     * @return LocatorInterface[] Object locators
     */
    public function findObjectPaths(SelectorInterface $selector, RepositoryInterface $repository)
    {
        chdir($this->root);

        // Build a glob string from the selector
        $glob = '';
        $globFlags = GLOB_ONLYDIR | GLOB_NOSORT;

        $year = $selector->getYear();
        if ($year !== null) {
            $glob .= '/'.$year;
        }

        $month = $selector->getMonth();
        if ($month !== null) {
            $glob .= '/'.$month;
        }

        $day = $selector->getDay();
        if ($day !== null) {
            $glob .= '/'.$day;
        }

        $hour = $selector->getHour();
        if ($hour !== null) {
            $glob .= '/'.$hour;
        }

        $minute = $selector->getMinute();
        if ($minute !== null) {
            $glob .= '/'.$minute;
        }

        $second = $selector->getSecond();
        if ($second !== null) {
            $glob .= '/'.$second;
        }

        $visibility = $selector->getVisibility();
        $uid = $selector->getId();
        $type = $selector->getType();
        if (($uid !== null) || ($type !== null)) {
            $glob .= '/'.($uid ?: SelectorInterface::WILDCARD).'-'.($type ?: SelectorInterface::WILDCARD);

            $revision = $selector->getRevision();
            if ($revision !== null) {
                $glob .= '/'.self::$globVisibilities[$visibility].($uid ?: SelectorInterface::WILDCARD).'-'.$revision;
                $globFlags &= ~GLOB_ONLYDIR;
            }
        }

        return array_map(
            function ($objectResourcePath) use ($repository) {
                return Kernel::create(RepositoryLocator::class, [$repository, '/'.$objectResourcePath]);
            },
            glob(ltrim($glob, '/'), $globFlags)
        );
    }

    /**
     * Return an individual hash for a resource
     *
     * @param string $resourcePath Repository relative resource path
     * @return string|null Resource hash
     */
    public function getResourceHash($resourcePath)
    {
        return $this->hasResource($this->root.$resourcePath) ? File::hash($this->root.$resourcePath) : null;
    }

    /**
     * Test if an object resource exists
     *
     * @param string $resourcePath Repository relative resource path
     * @return boolean Object resource exists
     */
    public function hasResource($resourcePath)
    {
        return is_file($this->root.$resourcePath);
    }

    /**
     * Import a resource into this repository
     *
     * @param string $source Source resource
     * @param string $target Repository relative target resource locator
     * @return boolean Success
     */
    public function importResource($source, $target)
    {
        return copy($source, $this->root.$target);
    }

    /**
     * Find and return an object resource
     *
     * @param string $resourcePath Repository relative resource locator
     * @return ResourceInterface|AbstractResource Object resource
     */
    public function getObjectResource($resourcePath)
    {
        return ResourceFactory::createFromSource(AbstractFileReaderWriter::WRAPPER.$this->root.$resourcePath);
    }

    /**
     * Allocate an object ID and create an object resource
     *
     * @param \Closure $creator Object creation closure
     * @return ObjectInterface Object
     * @throws DomainRepositoryRuntimeException If no object could be created
     * @throws \Exception If another error occurs
     */
    public function createObjectResource(\Closure $creator)
    {
        $sizeDescriptor = null;

        try {
            // Open the size descriptor
            $sizeDescriptor = fopen($this->configDir.'size.txt', 'r+');

            // If a lock of the size descriptor can be acquired
            if (flock($sizeDescriptor, LOCK_EX)) {
                // Determine the current repository size
                $repositorySize = '';
                while (!feof($sizeDescriptor)) {
                    $repositorySize .= fread($sizeDescriptor, 8);
                }
                $repositorySize = intval(trim($repositorySize));

                // Instantiate the next consecutive object ID
                $nextObjectId = Kernel::create(Id::class, [++$repositorySize]);

                // Create & persist the object (bypassing the repository)
                $object = $creator($nextObjectId);
                $this->persistObject($object);

                // Dump the new repository size, unlock the size descriptor
                ftruncate($sizeDescriptor, 0);
                fwrite($sizeDescriptor, $repositorySize);
                fflush($sizeDescriptor);
                flock($sizeDescriptor, LOCK_UN);

                // Return the newly created object
                return $object;
            }

            // If no object could be created
            throw new DomainRepositoryRuntimeException(
                'The repository size descriptor is unlockable',
                DomainRepositoryRuntimeException::REPO_SIZE_DESCRIPTOR_UNLOCKABLE
            );

            // If any exception is thrown
        } catch (\Exception $e) {
            // Release the size descriptor lock
            if (is_resource($sizeDescriptor)) {
                flock($sizeDescriptor, LOCK_UN);
            }

            // Forward the thrown exception
            throw $e;
        }
    }

    /**
     * Persist an object in the repository
     *
     * @param ObjectInterface $object Object
     * @return AdapterStrategyInterface Self reference
     */
    public function persistObject(ObjectInterface $object)
    {
        // If the object has just been deleted
        if ($object->hasBeenDeleted()) {
            return $this->deleteObject($object);

            // Elseif the object has just been undeleted
        } elseif ($object->hasBeenUndeleted()) {
            return $this->undeleteObject($object);

            // If the object has just been published
        } elseif ($object->hasBeenPublished()) {
            $this->publishObject($object);
        }

        // Persist the object resource
        return $this->persistObjectResource($object);
    }

    /**
     * Delete all revisions of an object
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface Object
     */
    protected function deleteObject(ObjectInterface $object)
    {
        // Hide object directory
        $objContainerDir = dirname(dirname($this->getAbsoluteResourcePath($object->getRepositoryLocator())));
        $objContainerName = $object->getId()->getId().'-'.$object->getType()->getType();
        $objPublicContainer = $objContainerDir.DIRECTORY_SEPARATOR.$objContainerName;
        $objHiddenContainer = $objContainerDir.DIRECTORY_SEPARATOR.'.'.$objContainerName;
        if (file_exists($objPublicContainer)
            && is_dir($objPublicContainer)
            && !rename($objPublicContainer, $objHiddenContainer)
        ) {
            throw new RuntimeException(
                sprintf('Cannot hide object container "%s"', $objContainerName),
                RuntimeException::CANNOT_HIDE_OBJECT_CONTAINER
            );
        }

        // Delete all object revisions
        /** @var ObjectInterface $objectRevision */
        foreach ($object as $objectRevision) {
            $this->persistObjectResource($objectRevision->delete());
        }

        return $this;
    }

    /**
     * Build an absolute repository resource locator
     *
     * @param RepositoryLocatorInterface $repositoryLocator Repository locator
     * @return string Absolute repository resource locator
     */
    public function getAbsoluteResourcePath(RepositoryLocatorInterface $repositoryLocator)
    {
        return $this->root.str_replace(
            '/',
            DIRECTORY_SEPARATOR,
            $repositoryLocator->withExtension(getenv('OBJECT_RESOURCE_EXTENSION'))
        );
    }

    /**
     * Persist an object resource in the repository
     *
     * @param ObjectInterface $object Object
     * @return AdapterStrategyInterface Self reference
     */
    protected function persistObjectResource(ObjectInterface $object)
    {
        /** @var \Apparat\Object\Infrastructure\Resource $objectResource */
        $objectResource = ResourceFactory::createFromObject($object);

        // Create the absolute object resource path
        $objectResourcePath = $this->getAbsoluteResourcePath($object->getRepositoryLocator());

        /** @var Writer $fileWriter */
        $fileWriter = Kernel::create(
            Writer::class,
            [$objectResourcePath, Writer::FILE_CREATE | Writer::FILE_CREATE_DIRS | Writer::FILE_OVERWRITE]
        );
        $objectResource->dump($fileWriter);

        return $this;
    }

    /**
     * Undelete all revisions of an object
     *
     * @param ObjectInterface $object Object
     * @return ObjectInterface Object
     */
    protected function undeleteObject(ObjectInterface $object)
    {
        // Hide object directory
        $objContainerDir = dirname(dirname($this->getAbsoluteResourcePath($object->getRepositoryLocator())));
        $objContainerName = $object->getId()->getId().'-'.$object->getType()->getType();
        $objPublicContainer = $objContainerDir.DIRECTORY_SEPARATOR.$objContainerName;
        $objHiddenContainer = $objContainerDir.DIRECTORY_SEPARATOR.'.'.$objContainerName;
        if (file_exists($objHiddenContainer)
            && is_dir($objHiddenContainer)
            && !rename($objHiddenContainer, $objPublicContainer)
        ) {
            throw new RuntimeException(
                sprintf('Cannot unhide object container "%s"', $objContainerName),
                RuntimeException::CANNOT_UNHIDE_OBJECT_CONTAINER
            );
        }

        // Undelete all object revisions
        /** @var ObjectInterface $objectRevision */
        foreach ($object as $objectRevision) {
            $this->persistObjectResource($objectRevision->undelete());
        }

        return $this;
    }

    /**
     * Publish an object in the repository
     *
     * @param ObjectInterface $object
     */
    protected function publishObject(ObjectInterface $object)
    {
        $objectRepoLocator = $object->getRepositoryLocator();

        // If the object had been persisted as a draft: Remove the draft resource
        $objectDraftLocator = $objectRepoLocator->setRevision($object->getRevision()->setDraft(true));
        $absObjectDraftPath = $this->getAbsoluteResourcePath($objectDraftLocator);
        if (@file_exists($absObjectDraftPath)) {
            unlink($absObjectDraftPath);
        }

        // If it's not the first object revision: Rotate the previous revision resource
        $objectRevisionNumber = $object->getRevision()->getRevision();
        if ($objectRevisionNumber > 1) {
            // Build the "current" object repository locator
            $currentRevision = Revision::current();
            $curObjectResPath =
                $this->getAbsoluteResourcePath($objectRepoLocator->setRevision($currentRevision));

            // Build the previous object repository locator
            /** @var Revision $previousRevision */
            $previousRevision = Kernel::create(Revision::class, [$objectRevisionNumber - 1]);
            $prevObjectResPath
                = $this->getAbsoluteResourcePath($objectRepoLocator->setRevision($previousRevision));

            // Rotate the previous revision's resource path
            if (file_exists($curObjectResPath)) {
                rename($curObjectResPath, $prevObjectResPath);
            }
        }
    }

    /**
     * Return the repository size (number of objects in the repository)
     *
     * @return int Repository size
     */
    public function getRepositorySize()
    {
        $sizeDescriptorFile = $this->configDir.'size.txt';
        $repositorySize = 0;
        if (is_file($sizeDescriptorFile) && is_readable($sizeDescriptorFile)) {
            $repositorySize = intval(file_get_contents($this->configDir.'size.txt'));
        }
        return $repositorySize;
    }
}
