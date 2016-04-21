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
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Domain\Repository\RuntimeException;
use Apparat\Object\Domain\Repository\Selector;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Object\Infrastructure\Factory\ResourceFactory;
use Apparat\Resource\Infrastructure\Io\File\AbstractFileReaderWriter;

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
     * Example
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

        // If the root directory configuration is invalid
        $this->root = realpath($this->config['root']);
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
     * Find objects by selector
     *
     * @param Selector|SelectorInterface $selector Object selector
     * @param RepositoryInterface $repository Object repository
     * @return array[PathInterface] Object paths
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
        return ResourceFactory::create(AbstractFileReaderWriter::WRAPPER.$this->root.$resourcePath);
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

    /**
     * Initialize the repository
     *
     * @return void
     * @throws RuntimeException If the repository cannot be initialized
     * @throws RuntimeException If the repository size descriptor can not be created
     */
    public function initializeRepository()
    {
        // If the repository cannot be initialized
        if (!is_dir($this->configDir) && !mkdir($this->configDir, 0777, true)) {
            throw new RuntimeException('Could not initialize repository', RuntimeException::REPO_NOT_INITIALIZED);
        }

        // If the repository size descriptor can not be created
        if (!@is_file($this->configDir.'size.txt') && !file_put_contents($this->configDir.'size.txt', '0')) {
            throw new RuntimeException(
                'Could not create repository size descriptor',
                RuntimeException::REPO_SIZE_DESCRIPTOR_NOT_CREATED
            );
        }
    }
}
