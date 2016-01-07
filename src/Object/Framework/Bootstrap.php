<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framework
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

namespace Apparat\Object\Framework;

/**
 * Test whether a URL is absolute and doesn't have query parameters and / or a fragment
 *
 * @param string $url URL
 * @return boolean If the URL is absolut and has neither query parameters or a fragment
 * @throws \RuntimeException If the URL is not absolute / valid
 * @throws \RuntimeException If the URL has query parameters
 * @throws \RuntimeException If the URL has a fragment
 */
function isAbsoluteBareUrl($url)
{
	if (!filter_var($url) || !preg_match("%^https?\:\/\/%i", $url)) {
		throw new \RuntimeException(sprintf('Apparat base URL "%s" must be valid', $url), 1451776352);
	}
	if (strlen(parse_url($url, PHP_URL_QUERY))) {
		throw new \RuntimeException(sprintf('Apparat base URL "%s" must not contain query parameters', $url),
			1451776509);
	}
	if (strlen(parse_url($url, PHP_URL_FRAGMENT))) {
		throw new \RuntimeException(sprintf('Apparat base URL "%s" must not contain a fragment', $url), 1451776570);
	}
	return true;
}

// Instantiate Dotenv
$dotenv = new \Dotenv\Dotenv(dirname(dirname(dirname(__DIR__))));
if (getenv('APP_ENV') === 'development') {
	$dotenv->load();
}

// Validate the required environment variables
$dotenv->required('APPARAT_BASE_URL')->notEmpty();
$dotenv->required('OBJECT_RESOURCE_EXTENSION')->notEmpty();
$dotenv->required('OBJECT_DATE_PRECISION')->isInteger()->allowedValues([0, 1, 2, 3, 4, 5, 6]);

// In-depth validation of the apparat base URL
$apparatBaseUrl = getenv('APPARAT_BASE_URL');
isAbsoluteBareUrl($apparatBaseUrl);

// Normalize the apparat base URL
putenv('APPARAT_BASE_URL='.rtrim($apparatBaseUrl, '/').'/');

// Unset global variables
unset($dotenv);
unset($apparatBaseUrl);