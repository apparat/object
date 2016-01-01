<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Object\Domain
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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

namespace Apparat\Object\Domain\Model\Path;

/**
 * Object invalid argument exception
 *
 * @package Apparat\Object\Domain
 */
class InvalidArgumentException extends \InvalidArgumentException
{
	/**
	 * Invalid date precision
	 *
	 * @var int
	 */
	const INVALID_DATE_PRECISION = 1451514114;
	/**
	 * Invalid object URL path
	 *
	 * @var int
	 */
	const INVALID_OBJECT_URL_PATH = 1449874494;
	/**
	 * Invalid object URL
	 *
	 * @var int
	 */
	const INVALID_OBJECT_URL = 1449873819;
	/**
	 * Invalid remote object URL
	 *
	 * @var int
	 */
	const UNALLOWED_REMOTE_OBJECT_URL = 1451515385;
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
	/**
	 * Invalid Apparat URL
	 *
	 * @var int
	 */
	const INVALID_APPARAT_URL = 1451435429;
}