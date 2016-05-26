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
use Apparat\Object\Domain\Model\Path\ObjectUrl;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Repository\SelectorInterface;

/**
 * Object facade
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Ports
 */
class Object
{
    /**
     * Private
     *
     * @var string
     */
    const PRIVACY_PRIVATE = MetaProperties::PRIVACY_PRIVATE;
    /**
     * Public
     *
     * @var string
     */
    const PRIVACY_PUBLIC = MetaProperties::PRIVACY_PUBLIC;
    /**
     * Visible
     *
     * @var int
     */
    const VISIBILITY_VISIBLE = SelectorInterface::VISIBLE;
    /**
     * Hidden
     *
     * @var int
     */
    const VISIBILITY_HIDDEN = SelectorInterface::HIDDEN;
    /**
     * Visible and hidden
     *
     * @var int
     */
    const VISIBILITY_ALL = SelectorInterface::ALL;

    /**
     * Instantiate and return an object
     *
     * @param string $url Object URL (relative or absolute including the apparat base URL)
     * @param int $visibility Object visibility
     * @return ObjectInterface Object
     * @api
     */
    public static function instance($url, $visibility = self::VISIBILITY_ALL)
    {
        // Instantiate the object URL
        /** @var ObjectUrl $objectUrl */
        $objectUrl = Kernel::create(ObjectUrl::class, [$url, true]);

        // Instantiate the local object repository, load and return the object
        return Repository::instance($objectUrl->getRepositoryUrl())->loadObject($objectUrl, $visibility);
    }
}
