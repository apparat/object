<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\<Layer>
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

namespace Apparat\Object\Framework\Repository;

/**
 * Repository invalid argument exception
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Framework
 */
class InvalidArgumentException extends \InvalidArgumentException
{
	/**
	 * Empty adapter strategy configuration
	 *
	 * @var int
	 */
	const EMPTY_ADAPTER_STRATEGY_CONFIG = 1449956347;
	/**
	 * Invalid adapter strategy type
	 *
	 * @var int
	 */
	const INVALID_ADAPTER_STRATEGY_TYPE = 1449956471;
	/**
	 * Empty file adapter strategy root
	 *
	 * @var int
	 */
	const EMTPY_FILE_STRATEGY_ROOT = 1449956977;
	/**
	 * Invalid file adapter strategy root
	 *
	 * @var int
	 */
	const INVALID_FILE_STRATEGY_ROOT = 1449957017;

}