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

use Apparat\Object\Application\Model\Object\Manager;
use Apparat\Object\Domain\Repository\Register;
use Apparat\Object\Framework\Factory\AdapterStrategyFactory;
use Apparat\Object\Framework\Repository\InvalidArgumentException;

/**
 * Repository facade
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Framework
 */
class Repository
{

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Register a repository
	 *
	 * @param string $url Repository URL (relative or absolute including the apparat base URL)
	 * @param array $config Repository configuration
	 * @throws InvalidArgumentException If the repository URL is invalid
	 * @throws InvalidArgumentException If the repository configuration is empty
	 * @api
	 */
	public static function register($url, array $config)
	{
		// Normalize to local repository URL
		try {
			$url = self::_normalizeRepositoryUrl($url);
		} catch (\RuntimeException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}

		// If the repository configuration is empty
		if (!count($config)) {
			throw new InvalidArgumentException('Empty repository configuration',
				InvalidArgumentException::EMPTY_REPOSITORY_CONFIG);
		}

		// Instantiate the repository adapter strategy
		$repositoryAdapterStrategy = AdapterStrategyFactory::create($config);

		// Instantiate and register the object repository
		$repository = new \Apparat\Object\Domain\Repository\Repository($url, $repositoryAdapterStrategy, new Manager());

		// Register the repository
		Register::register($url, $repository);
	}

	/**
	 * Instantiate and return an object repository
	 *
	 * @param string $url Repository URL (relative or absolute including the apparat base URL)
	 * @return \Apparat\Object\Domain\Repository\Repository Object repository
	 * @throws InvalidArgumentException If the repository URL is invalid
	 * @throws InvalidArgumentException If the repository URL is unknown
	 * @api
	 */
	public static function instance($url)
	{
		// Normalize to local repository URL
		try {
			$url = self::_normalizeRepositoryUrl($url);
		} catch (\RuntimeException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}

		// Return the repository instance
		return Register::instance($url);
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Normalize the repository URL
	 *
	 * @param string $url Public repository URL
	 * @return bool|string Normalized repository URL
	 * @throws InvalidArgumentException If the repository URL is external
	 */
	protected static function _normalizeRepositoryUrl($url)
	{
		// Strip the leading apparat base URL
		$apparatBaseUrl = getenv('APPARAT_BASE_URL');
		if (strpos($url, $apparatBaseUrl) === 0) {
			$url = substr($url, strlen($apparatBaseUrl));
		}

		// Strip leading slashes
		$url = ltrim($url, '/');

		// If this is still a valid absolute URL, it must be external
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			throw new InvalidArgumentException(sprintf('External respository URL "%s" not allowed', $url),
				InvalidArgumentException::EXTERNAL_REPOSITORY_URL_NOT_ALLOWED);
		}

		// Ensure this is a bare URL (without query and fragment)
		return \Apparat\Object\Framework\isAbsoluteBareUrl($apparatBaseUrl.$url) ? $url : false;
	}
}