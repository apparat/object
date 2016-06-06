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

namespace Apparat\Object\Domain\Model\Properties;

/**
 * Object properties invalid argument exception
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * Empty property name
     *
     * @var int
     */
    const EMPTY_PROPERTY_NAME = 1450817720;

    /**
     * Invalid property name
     *
     * @var int
     */
    const INVALID_PROPERTY_NAME = 1450818168;

    /**
     * Empty property collection name
     *
     * @var int
     */
    const EMPTY_COLLECTION_NAME = 1450821755;

    /**
     * Invalid property collection name
     *
     * @var int
     */
    const INVALID_COLLECTION_NAME = 1450821628;

    /**
     * Invalid object type
     *
     * @var int
     */
    const INVALID_OBJECT_TYPE = 1450824343;
    /**
     * Invalid object author
     *
     * @var int
     */
    const INVALID_OBJECT_AUTHOR = 1451425516;
    /**
     * Invalid domain property collection class
     *
     * @var int
     */
    const INVALID_DOMAIN_PROPERTY_COLLECTION_CLASS = 1452288429;
    /**
     * Invalid system properties
     *
     * @var int
     */
    const INVALID_SYSTEM_PROPERTIES = 1456522289;
    /**
     * Invalid object relation
     *
     * @var int
     */
    const INVALID_OBJECT_RELATION = 1462703468;
    /**
     * Invalid location property value
     *
     * @var int
     */
    const INVALID_LOCATION_PROPERTY_VALUE = 1462903252;
}
