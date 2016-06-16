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

use Apparat\Object\Domain\Model\Uri\RepositoryLocatorInterface;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Domain\Repository\SelectorInterface;

/**
 * Object manager interface
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
interface ManagerInterface
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
    );

    /**
     * Load an object from a repository
     *
     * @param RepositoryLocatorInterface $locator Repository object locator
     * @param int $visibility Object visibility
     * @return ObjectInterface Object
     */
    public function loadObject(RepositoryLocatorInterface $locator, $visibility = SelectorInterface::ALL);

    /**
     * Load and return an object resource respecting visibility constraints
     *
     * @param RepositoryLocatorInterface $currentLocator
     * @param int $visibility Object visibility
     * @return ResourceInterface Object resource
     */
    public function loadObjectResource(RepositoryLocatorInterface &$currentLocator, $visibility = SelectorInterface::ALL);

    /**
     * Test whether an object resource exists
     *
     * @param RepositoryLocatorInterface $locator
     * @return boolean Object resource exists
     */
    public function objectResourceExists(RepositoryLocatorInterface $locator);
}
