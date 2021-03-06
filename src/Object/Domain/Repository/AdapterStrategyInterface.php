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

namespace Apparat\Object\Domain\Repository;

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Domain\Model\Uri\LocatorInterface;
use Apparat\Object\Domain\Model\Uri\RepositoryLocatorInterface;

/**
 * Repository adapter strategy interface
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
interface AdapterStrategyInterface
{
    /**
     * Find objects by selector
     *
     * @param SelectorInterface $selector Object selector
     * @param RepositoryInterface $repository Object repository
     * @return LocatorInterface[] Object locators
     */
    public function findObjectResourceLocators(SelectorInterface $selector, RepositoryInterface $repository);

    /**
     * Find and return an object resource
     *
     * @param string $resourcePath Repository relative resource path
     * @return ResourceInterface Object resource
     */
    public function getObjectResource($resourcePath);

    /**
     * Test if an object resource exists
     *
     * @param string $resourcePath Repository relative resource path
     * @return boolean Object resource exists
     */
    public function hasResource($resourcePath);

    /**
     * Return an individual hash for a resource
     *
     * @param string $resourcePath Repository relative resource path
     * @return string|null Resource hash
     */
    public function getResourceHash($resourcePath);

    /**
     * Import a resource into this repository
     *
     * @param string $source Source resource
     * @param string $target Repository relative target resource locator
     * @return boolean Success
     */
    public function importResource($source, $target);

    /**
     * Allocate an object ID and create an object resource
     *
     * @param \Closure $creator Object creation closure
     * @return ObjectInterface Object
     */
    public function createObjectResource(\Closure $creator);

    /**
     * Persist an object in the repository
     *
     * @param ObjectInterface $object Object
     * @return AdapterStrategyInterface Self reference
     */
    public function persistObject(ObjectInterface $object);

    /**
     * Return the adapter strategy type
     *
     * @return string Adapter strategy type
     */
    public function getAdapterType();

    /**
     * Return the repository size (number of objects in the repository)
     *
     * @return int Repository size
     */
    public function getRepositorySize();

    /**
     * Initialize the repository
     *
     * @return boolean Success
     */
    public function initializeRepository();

    /**
     * Build an absolute repository resource locator
     *
     * @param RepositoryLocatorInterface $repositoryLocator Repository locator
     * @return string Absolute repository resource locator
     */
    public function getAbsoluteResourcePath(RepositoryLocatorInterface $repositoryLocator);
}
