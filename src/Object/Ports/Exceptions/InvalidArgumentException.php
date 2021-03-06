<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\<Layer>
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

namespace Apparat\Object\Ports\Exceptions;

/**
 * Invalid argument exception
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Ports
 */
class InvalidArgumentException extends \InvalidArgumentException
{
    /**
     * Invalid binary payload source
     *
     * @var int
     */
    const INVALID_BINARY_PAYLOAD_SOURCE = 1464296678;
    /**
     * Invalid apparat object property
     *
     * @var int
     */
    const INVALID_APPARAT_OBJECT_PROPERTY = 1465330399;
    /**
     * Cannot set apparat object property
     *
     * @var int
     */
    const CANNOT_SET_APPARAT_OBJECT_PROPERTY = 1466804125;
    /**
     * Cannot unset apparat object property
     *
     * @var int
     */
    const CANNOT_UNSET_APPARAT_OBJECT_PROPERTY = 1465330565;
    /**
     * Cannot append apparat object property
     *
     * @var int
     */
    const CANNOT_APPEND_APPARAT_OBJECT_VALUE = 1466804193;
    /**
     * Invalid exchange object
     *
     * @var int
     */
    const INVALID_EXCHANGE_OBJECT = 1466805183;
    /**
     * Unknown apparat object type
     *
     * @var int
     */
    const UNKNOWN_APPARAT_OBJECT_TYPE = 1465368597;
}
