<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Apparat\Object\Infrastructure\Model\Object
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
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

namespace Apparat\Object\Infrastructure\Model\Object;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Uri\ObjectUrl;
use Apparat\Object\Infrastructure\Repository\Repository;
use Apparat\Object\Ports\Types\Object as ObjectTypes;

/**
 * Object gateway
 *
 * @package Apparat\Server
 * @subpackage Apparat\Object\Infrastructure\Model\Object
 */
class Object
{
    /**
     * Instantiate and return an object
     *
     * @param string $url Object URL (relative or absolute including the apparat base URL)
     * @param int $visibility Object visibility
     * @return ObjectInterface Object
     */
    public static function load($url, $visibility = ObjectTypes::VISIBILITY_ALL)
    {
        // Instantiate the object URL
        /** @var ObjectUrl $objectUrl */
        $objectUrl = Kernel::create(ObjectUrl::class, [$url, true]);

        // Instantiate the local object repository, load and return the object
        return Repository::instance($objectUrl->getRepositoryUrl())->loadObject($objectUrl, $visibility);
    }
}
