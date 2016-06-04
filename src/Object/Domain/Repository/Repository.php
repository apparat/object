<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Repository;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\Collection;
use Apparat\Object\Domain\Model\Object\ManagerInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Path\PathInterface;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;

/**
 * Abstract object repository
 *
 * @package Apparat\Object\Domain\Repository
 */
class Repository implements RepositoryInterface
{
    /**
     * Apparat base URL
     *
     * @var string
     */
    protected $url = null;
    /**
     * Adapter strategy
     *
     * @var AdapterStrategyInterface
     */
    protected $adapterStrategy = null;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Repository constructor
     *
     * @param string $url Apparat base URL
     * @param array $config Adapter strategy configuration
     */
    public function __construct(
        $url,
        array $config
    ) {
        $this->url = rtrim('/'.$url, '/');
        $this->adapterStrategy = Kernel::create(Service::class)->getAdapterStrategyFactory()->createFromConfig($config);
    }

    /**
     * Find objects by selector
     *
     * @param SelectorInterface $selector Object selector
     * @return Collection Object collection
     */
    public function findObjects(SelectorInterface $selector)
    {
        return Kernel::create(Collection::class, [$this->adapterStrategy->findObjectPaths($selector, $this)]);
    }

    /**
     * Create an object and add it to the repository
     *
     * @param string|Type $type Object type
     * @param string $payload Object payload
     * @param array $propertyData Object property data
     * @param \DateTimeInterface $creationDate Object creation date
     * @return ObjectInterface Object
     */
    public function createObject(
        $type,
        $payload = '',
        array $propertyData = [],
        \DateTimeInterface $creationDate = null
    ) {
        // Instantiate the object type
        if (!($type instanceof Type)) {
            /** @var Type $type */
            $type = Kernel::create(Type::class, [$type]);
        }

        /** @var ManagerInterface $objectManager */
        $objectManager = Kernel::create(Service::class)->getObjectManager();

        // Create and return the object
        return $objectManager->createObject($this, $type, $payload, $propertyData, $creationDate);
    }

    /**
     * Delete and object from the repository
     *
     * @param ObjectInterface $object Object
     * @return boolean Success
     */
    public function deleteObject(ObjectInterface $object)
    {
        $this->adapterStrategy->persistObject($object->delete());
        return true;
    }

    /**
     * Update an object in the repository
     *
     * @param ObjectInterface $object Object
     * @return bool Success
     */
    public function updateObject(ObjectInterface $object)
    {
        $this->adapterStrategy->persistObject($object);
        return true;
    }

    /**
     * Load an object from this repository
     *
     * @param PathInterface $path Object path
     * @param int $visibility Object visibility
     * @return ObjectInterface Object
     */
    public function loadObject(PathInterface $path, $visibility = SelectorInterface::ALL)
    {
        /** @var ManagerInterface $objectManager */
        $objectManager = Kernel::create(Service::class)->getObjectManager();

        /** @var RepositoryPathInterface $repositoryPath */
        $repositoryPath = Kernel::create(RepositoryPath::class, [$this, $path]);

        // Load and return the object
        return $objectManager->loadObject($repositoryPath, $visibility);
    }

    /**
     * Return the repository's adapter strategy
     *
     * @return AdapterStrategyInterface Adapter strategy
     */
    public function getAdapterStrategy()
    {
        return $this->adapterStrategy;
    }

    /**
     * Return the repository URL (relative to Apparat base URL)
     *
     * @return string Repository URL
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return the repository size (number of objects in the repository)
     *
     * @return int Repository size
     */
    public function getSize()
    {
        // TODO: Implement getSize() method.
    }
}
