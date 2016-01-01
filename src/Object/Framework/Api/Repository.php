<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\<Layer>
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

use Apparat\Object\Framework\Model\Object\Manager;
use Apparat\Object\Framework\Repository\AdapterStrategyFactory;

/**
 * Repository factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Framework
 */
class Repository
{
	/**
	 * Instanciate and return an object repository
	 *
	 * @param array $config Repository configuration
	 * @return \Apparat\Object\Domain\Repository\Repository Object repository
	 * @throws InvalidArgumentException If the repository configuration is empty
	 * @throws InvalidArgumentException If the apparat base URL is not defined
	 * @api
	 */
	public static function create(array $config)
	{
		// If no repositories are configured
		if (!count($config)) {
			throw new InvalidArgumentException('Empty repository configuration',
				InvalidArgumentException::EMPTY_REPOSITORY_CONFIG);
		}

		// If the apparat base URL is not defined
		if (empty($config['url'])) {
			throw new InvalidArgumentException('Missing apparat base URL',
				InvalidArgumentException::MISSING_APPARAT_BASE_URL);
		}

		// Instantiate the repository adapter strategy
		$repositoryAdapterStrategy = AdapterStrategyFactory::create($config);

		// Instantiate and return the object repository
		return \Apparat\Object\Domain\Repository\Repository::instance($config['url'], $repositoryAdapterStrategy, new Manager());
	}
}