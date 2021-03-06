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

namespace Apparat\Object\Domain\Model\Object;

/**
 * Object invalid argument exception
 *
 * @package Apparat\Object\Domain
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * Unknown object ID
     *
     * @var int
     */
    const UNKNOWN_OBJECT_ID = 1448737190;
    /**
     * Invalid object type
     *
     * @var int
     */
    const INVALID_OBJECT_TYPE = 1449871242;
    /**
     * Invalid object ID
     *
     * @var int
     */
    const INVALID_OBJECT_ID = 1449876361;
    /**
     * Invalid object revision number
     *
     * @var int
     */
    const INVALID_OBJECT_REVISION = 1449871715;
    /**
     * Invalid collection object or locator
     *
     * @var int
     */
    const INVALID_COLLECTION_OBJECT_OR_LOCATOR = 1450131914;
    /**
     * Invalid object proxy method
     *
     * @var int
     */
    const INVALID_OBJECT_PROXY_METHOD = 1451431111;
}
