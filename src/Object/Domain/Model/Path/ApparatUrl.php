<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

use Apparat\Object\Domain\Repository\Register;

/**
 * Apparat URL
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class ApparatUrl extends ObjectUrl
{
	/**
	 * Valid schemes
	 *
	 * @var array
	 */
	protected static $_schemes = [self::SCHEME_APRT => true, self::SCHEME_APRTS => true];

	/**
	 * APRT-Schema
	 *
	 * @var string
	 */
	const SCHEME_APRT = 'aprt';
	/**
	 * APRTS-Schema
	 *
	 * @var string
	 */
	const SCHEME_APRTS = 'aprts';

	/**
	 * Apparat URL constructor
	 *
	 * If the constructor doesn't throw an exception, the URL is valid and
	 *
	 * 1. either an absolute URL (local or remote) or
	 * 2. a relative URL to a known local repository
	 *
	 * @param string $url Apparat URL
	 * @param boolean $remote Accept remote URL (less strict date component checking)
	 * @throws ApparatInvalidArgumentException If the URL is absolute but doesn't have the apparat scheme
	 * @throws ApparatInvalidArgumentException If this is a local Apparat URL with an unknown repository
	 */
	public function __construct($url, $remote = false)
	{
		parent::__construct($url, $remote);

		// If the URL is absolute but doesn't have the apparat scheme
		if ($this->isAbsolute() && !array_key_exists($this->_urlParts['scheme'], self::$_schemes)) {
			throw new ApparatInvalidArgumentException(sprintf('Invalid absolute apparat URL "%s"', $url), ApparatInvalidArgumentException::INVALID_ABSOLUTE_APPARAT_URL);
		}

		// If this is a local Apparat URL with an unknown repository
		if (!$this->isAbsolute() && !Register::isRegistered($this->getPath())) {
			throw new ApparatInvalidArgumentException(sprintf('Unknown local repository URL "%s"', $this->getPath()), ApparatInvalidArgumentException::UNKNOWN_LOCAL_REPOSITORY_URL);
		}
	}
}