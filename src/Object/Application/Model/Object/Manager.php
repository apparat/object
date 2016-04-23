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

use Apparat\Object\Application\Factory\ObjectFactory;
use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\ManagerInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Path\RepositoryPathInterface;
use Apparat\Object\Domain\Repository\RepositoryInterface;

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
        $creationClosure = function (Id $id) use ($type, $payload, $propertyData) {
            
            
            return ObjectFactory::createFromParams($id, $type, $payload, $propertyData);
        };

        // Wrap the object creation in an ID allocation transaction
        return $repository->getAdapterStrategy()->createObjectResource($creationClosure);
    }

    /**
     * Load an object from a repository
     *
     * @param RepositoryPathInterface $path Repository object path
     * @return ObjectInterface Object
     */
    public function loadObject(RepositoryPathInterface $path)
    {
        /** @var \Apparat\Object\Infrastructure\Model\Object\Resource $objectResource */
        $objectResource = $path->getRepository()->getAdapterStrategy()->getObjectResource(
            $path->withExtension(getenv('OBJECT_RESOURCE_EXTENSION'))
        );

        return ObjectFactory::createFromResource($objectResource, $path);
    }
}
