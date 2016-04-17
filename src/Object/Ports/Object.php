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

namespace Apparat\Object\Ports;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Path\ObjectUrl;
use Apparat\Object\Domain\Repository\Service;

/**
 * Object facade
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Ports
 */
class Object
{
    /**
     * Instantiate and return an object
     *
     * @param string $url Object URL (relative or absolute including the apparat base URL)
     * @return ObjectInterface Object
     * @api
     */
    public static function instance($url)
    {
        // Instantiate the object URL
        $objectUrl = new ObjectUrl($url, true);

        // Instantiate the local object repository, load and return the object
        return Repository::instance($objectUrl->getRepositoryUrl())->loadObject($objectUrl);
    }


    /**
     * Create and return an object
     *
     * @param string|Type $type Object type
     * @param string $payload Object payload
     * @param array $propertyData Object property data
     * @return ObjectInterface Object
     */
    public static function create($type, $payload = '', array $propertyData = []) {

        // Instantiate the object type
        if (!($type instanceof Type)) {
            $type = new Type($type);
        }

        // Create and return the new object
        return Kernel::create(Service::class)->getObjectManager()->createObject($type, $payload, $propertyData);
    }
}
