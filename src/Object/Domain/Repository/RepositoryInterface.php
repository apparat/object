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

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Path\PathInterface;

/**
 * Object repository interface
 *
 * @package Apparat\Object\Domain\Repository
 */
interface RepositoryInterface extends SearchableRepositoryInterface
{
    /**
     * Initialize the repository
     *
     * @return void
     */
    public function initialize();

    /**
     * Create an object and add it to the repository
     *
     * @param string|Type $type Object type
     * @param string $payload Object payload
     * @param array $propertyData Object property data
     * @return ObjectInterface Object
     */
    public function createObject($type, $payload = '', array $propertyData = []);

    /**
     * Delete and object from the repository
     *
     * @param ObjectInterface $object Object
     * @return boolean Success
     */
    public function deleteObject(ObjectInterface $object);

    /**
     * Update an object in the repository
     *
     * @param ObjectInterface $object Object
     * @return bool Success
     */
    public function updateObject(ObjectInterface $object);

    /**
     * Load an object from this repository
     *
     * @param PathInterface $path Object path
     * @return ObjectInterface Object
     */
    public function loadObject(PathInterface $path);

    /**
     * Return the repository's adapter strategy
     *
     * @return AdapterStrategyInterface Adapter strategy
     */
    public function getAdapterStrategy();

    /**
     * Return the repository URL (relative to Apparat base URL)
     *
     * @return string Repository URL
     */
    public function getUrl();

    /**
     * Return the repository size (number of objects in the repository)
     *
     * @return int Repository size
     */
    public function getSize();
}
