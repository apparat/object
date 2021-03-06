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

namespace Apparat\Object\Infrastructure\Factory;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Infrastructure\Resource;

/**
 * Object resource factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 */
class ResourceFactory extends \Apparat\Resource\Ports\Resource
{
    /**
     * Create and return a FrontMark resource instance from as resource string
     *
     * @param string $src Stream-wrapped source
     * @param array $parameters Reader parameters
     * @return ResourceInterface Object resource
     */
    public static function createFromSource($src, ...$parameters)
    {
        return self::fromSource($src, Resource::class, ...$parameters);
    }

    /**
     * Create and return a FrontMark resource instance from an object
     *
     * @param ObjectInterface $object Object
     * @return ResourceInterface Object resource
     */
    public static function createFromObject(ObjectInterface $object)
    {
        /** @var ResourceInterface $resource */
        $resource = Kernel::create(Resource::class, [null]);
        $resource->setPropertyData($object->getPropertyData());
        $resource->setPayload($object->getPayload());
        return $resource;
    }
}
