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
use Apparat\Object\Domain\Model\Path\PathInterface;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Domain\Repository\RuntimeException;
use Apparat\Object\Domain\Repository\Selector;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Object\Infrastructure\Factory\ResourceFactory;
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

        // Get the real path of the root directory
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
     * @throws RuntimeException If the repository cannot be initialized
     * @throws RuntimeException If the repository size descriptor can not be created
     */
    public function initializeRepository()
    {
        $configDir = $this->config['root'].DIRECTORY_SEPARATOR.'.repo'.DIRECTORY_SEPARATOR;

        // If the repository cannot be initialized
        if (!is_dir($configDir) && !mkdir($configDir, 0777, true)) {
            throw new RuntimeException('Could not initialize repository', RuntimeException::REPO_NOT_INITIALIZED);
        }

        // If the repository size descriptor can not be created
        if (!@is_file($configDir.'size.txt') && !file_put_contents($configDir.'size.txt', '0')) {
            throw new RuntimeException(
                'Could not create repository size descriptor',
                RuntimeException::REPO_SIZE_DESCRIPTOR_NOT_CREATED
            );
        }

        return true;
    }

    /**
     * Find objects by selector
     *
     * @param Selector|SelectorInterface $selector Object selector
     * @param RepositoryInterface $repository Object repository
     * @return PathInterface[] Object paths
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

        $uid = $selector->getId();
        $type = $selector->getType();
        if (($uid !== null) || ($type !== null)) {
            $glob .= '/'.($uid ?: Selector::WILDCARD).'.'.($type ?: Selector::WILDCARD);

            $revision = $selector->getRevision();
            if ($revision !== null) {
                $glob .= '/'.($uid ?: Selector::WILDCARD).'-'.$revision;
                $globFlags &= ~GLOB_ONLYDIR;
            }
        }

        return array_map(
            function ($objectPath) use ($repository) {
                return Kernel::create(RepositoryPath::class, [$repository, '/'.$objectPath]);
            },
            glob(ltrim($glob, '/'), $globFlags)
        );
    }

    /**
     * Find and return an object resource
     *
     * @param string $resourcePath Repository relative resource path
     * @return ResourceInterface Object resource
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

                // Create & persist the object
                $object = $this->persistObject($creator($nextObjectId));

                // Dump the new repository size, unlock the size descriptor
                ftruncate($sizeDescriptor, 0);
                fwrite($sizeDescriptor, $repositorySize);
                fflush($sizeDescriptor);
                flock($sizeDescriptor, LOCK_UN);

                // Return the newly created object
                return $object;
            }

            // Throw an error if no object could be created
            throw new RuntimeException(
                'The repository size descriptor is unlockable',
                RuntimeException::REPO_SIZE_DESCRIPTOR_UNLOCKABLE
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
     * @return ObjectInterface Persisted object
     */
    public function persistObject(ObjectInterface $object)
    {
        /** @var \Apparat\Object\Infrastructure\Model\Object\Resource $objectResource */
        $objectResource = ResourceFactory::createFromObject($object);

        // Create the absolute object resource path
        $objectResourcePath = $this->root.str_replace('/', DIRECTORY_SEPARATOR,
                $object->getRepositoryPath()->withExtension(getenv('OBJECT_RESOURCE_EXTENSION')));

        /** @var Writer $fileWriter */
        $fileWriter = Kernel::create(
            Writer::class,
            [$objectResourcePath, Writer::FILE_CREATE | Writer::FILE_CREATE_DIRS | Writer::FILE_OVERWRITE]
        );
        $objectResource->dump($fileWriter);

        // TODO: Set object clean

        return $object;
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
