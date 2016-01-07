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

namespace Apparat\Object\Framework\Api;

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Path\ObjectUrl;
use Apparat\Object\Domain\Model\Path\Url;

/**
 * Object facade
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Framework
 */
class Object
{
	/**
	 * Instantiate and return an object
	 *
	 * @param string $url Object URL (relative or absolute including the apparat base URL)
	 * @return ObjectInterface Object
	 * @api
	 */
	public static function instance($url)
	{
		// Instantiate the apparat base URL
		$apparatBaseUrl = new Url(getenv('APPARAT_BASE_URL'));
		$apparatBaseUrlPath = $apparatBaseUrl->getPath();

		// Instantiate the object URL
		$objectUrl = new ObjectUrl($url, true);

		// If the object URL matches matches the apparat instance
		if ($objectUrl->matches($apparatBaseUrl->setPath('')) && !strncmp($apparatBaseUrlPath, $objectUrl->getPath(),
				strlen($apparatBaseUrlPath))
		) {
			$repoUrl = substr($objectUrl->getPath(), strlen($apparatBaseUrlPath));

			// Else: If it's not an absolute object URL
		} elseif(!$objectUrl->isAbsolute()) {
			$repoUrl = $objectUrl->getPath();

			// Else: Remote repository
		} else {
			// TODO
			die('Remote repository TBD');
		}

		// Instantiate the object repository, load and return the object
		return Repository::instance($repoUrl)->loadObject($objectUrl);
	}
}