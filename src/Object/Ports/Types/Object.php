<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Ports
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

namespace Apparat\Object\Ports\Types;

use Apparat\Object\Application\Contract\ObjectTypesInterface;
use Apparat\Object\Application\Service\TypeService;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Repository\SelectorInterface;

/**
 * Object types & constants
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Ports
 */
class Object implements ObjectTypesInterface
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
     * Enable an object type
     *
     * @param string $type Object type
     */
    public static function enableType($type)
    {
        TypeService::enableType($type);
    }

    /**
     * Return whether a particular object type is supported
     *
     * @param string $type Object type
     * @return bool Object type is supported
     */
    public static function supportsType($type)
    {
        return TypeService::isEnabled($type);
    }

    /**
     * Return all supported object types
     *
     * @return array Supported object types
     */
    public static function getSupportedTypes()
    {
        return TypeService::getSupportedTypes();
    }
}
