<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat_<Package>
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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
 * @package Apparat\Object\Domain\Model\Url
 */
class InvalidArgumentException extends \InvalidArgumentException
{
	/**
	 * Unkown object ID
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
	 * Invalid object URL
	 *
	 * @var int
	 */
	const INVALID_OBJECT_URL = 1449873819;
	/**
	 * Invalid object URL path
	 *
	 * @var int
	 */
	const INVALID_OBJECT_URL_PATH = 1449874494;
	/**
	 * Invalid object URL scheme
	 *
	 * @var int
	 */
	const INVALID_OBJECT_URL_SCHEME = 1449924914;
	/**
	 * Invalid object URL host
	 *
	 * @var int
	 */
	const INVALID_OBJECT_URL_HOST = 1449925567;
	/**
	 * Invalid object URL port
	 *
	 * @var int
	 */
	const INVALID_OBJECT_URL_PORT = 1449925885;
}