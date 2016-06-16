<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Ports
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

namespace Apparat\Object\Ports\Facades;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Factory\SelectorFactory;
use Apparat\Object\Domain\Model\Object\Collection;
use Apparat\Object\Domain\Model\Uri\LocatorInterface;
use Apparat\Object\Domain\Model\Uri\RepositoryLocator;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Object\Infrastructure\Factory\ApparatObjectFactory;
use Apparat\Object\Ports\Contract\ApparatObjectInterface;

/**
 * Repository facade
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Ports
 */
class RepositoryFacade implements FacadeInterface
{
    /**
     * Repository
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Repository facade constructor
     *
     * @param RepositoryInterface $repository
     */
    protected function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Register a repository
     *
     * @param string $url Repository URL (relative or absolute including the apparat base URL)
     * @param array $config Repository configuration
     * @return RepositoryFacade Repository facade
     * @api
     */
    public static function register($url, array $config)
    {
        return new static(\Apparat\Object\Infrastructure\Repository\Repository::register($url, $config));
    }

    /**
     * Instantiate and return an object repository
     *
     * @param string $url Repository URL (relative or absolute including the apparat base URL)
     * @return RepositoryFacade Repository facade
     * @api
     */
    public static function instance($url)
    {
        return new static(\Apparat\Object\Infrastructure\Repository\Repository::instance($url));
    }

    /**
     * Create a repository
     *
     * @param string $url Repository URL (relative or absolute including the apparat base URL)
     * @param array $config Repository configuration
     * @return RepositoryFacade Repository facade
     * @api
     */
    public static function create($url, array $config)
    {
        return new static(\Apparat\Object\Infrastructure\Repository\Repository::create($url, $config));
    }

    /**
     * Find objects by selector
     *
     * @param string $selector Object selector
     * @return Collection Object collection
     * @todo Cast collection as apparat objects
     */
    public function findObjects($selector)
    {
        $selector = SelectorFactory::createFromString($selector);
        $collection = $this->repository->findObjects($selector);
        return $collection;
    }

    /**
     * Load an object from this repository
     *
     * @param string $path Object path
     * @param int $visibility Object visibility
     * @return ApparatObjectInterface Object
     */
    public function loadObject($path, $visibility = SelectorInterface::ALL)
    {
        /** @var LocatorInterface $objectPath */
        $objectPath = Kernel::create(RepositoryLocator::class, [$this->repository, $path]);
        return ApparatObjectFactory::create($this->repository->loadObject($objectPath, $visibility));
    }
}
