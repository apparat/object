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
use Apparat\Object\Framework\Factory\AdapterStrategyFactory;

/**
 * Repository facade
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Framework
 */
class Repository
{
	/**
	 * Registered repositories
	 *
	 * @var array
	 */
	protected static $_registry = [];

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Register a repository
	 *
	 * @param string $url Public repository URL (relative to apparat base URL)
	 * @param array $config Repository configuration
	 * @throws InvalidArgumentException If the public repository URL is invalid
	 * @throws InvalidArgumentException If the repository configuration is empty
	 * @api
	 */
	public static function register($url, array $config)
	{
		// Strip off the potentially leading apparat base URL
		try {
			$url = self::_normalizeRepositoryUrl($url);
		} catch(\RuntimeException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}

		// If the repository configuration is empty
		if (!count($config)) {
			throw new InvalidArgumentException('Empty repository configuration',
				InvalidArgumentException::EMPTY_REPOSITORY_CONFIG);
		}

		// Registration
		self::$_registry[$url] = [
			'config' => $config,
			'instance' => null,
		];
	}

	/**
	 * Instanciate and return an object repository
	 *
	 * @param string $url Public repository URL (relative to apparat base URL)
	 * @return \Apparat\Object\Domain\Repository\Repository Object repository
	 * @throws InvalidArgumentException If the public repository URL is invalid
	 * @throws InvalidArgumentException If the public repository URL is unknown
	 * @api
	 */
	public static function instance($url)
	{
		// Strip off the potentially leading apparat base URL
		try {
			$url = self::_normalizeRepositoryUrl($url);
		} catch(\RuntimeException $e) {
			throw new InvalidArgumentException($e->getMessage(), $e->getCode());
		}

		// If the public repository URL is unknown
		if (empty(self::$_registry[$url])) {
			throw new InvalidArgumentException(sprintf('Unknown public repository URL "%s"', $url),
				InvalidArgumentException::UNKNOWN_PUBLIC_REPOSITORY_URL);
		}

		if (!self::$_registry[$url]['instance'] instanceof \Apparat\Object\Domain\Repository\Repository) {

			// Instantiate the repository adapter strategy
			$repositoryAdapterStrategy = AdapterStrategyFactory::create(self::$_registry[$url]['config']);

			// Instantiate and return the object repository
			self::$_registry[$url]['instance'] = \Apparat\Object\Domain\Repository\Repository::instance($url,
				$repositoryAdapterStrategy, new Manager());
		}

		return self::$_registry[$url]['instance'];
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Normalize the public repository URL
	 *
	 * @param string $url Public repository URL
	 * @return bool|string Normalized repository URL
	 * @throws InvalidArgumentException If the respository URL is external
	 */
	protected static function _normalizeRepositoryUrl($url)
	{
		$apparatBaseUrl = getenv('APPARAT_BASE_URL');
		$url = ltrim((strpos($url, $apparatBaseUrl) === 0) ? substr($url, strlen($apparatBaseUrl)) : $url, '/');

		// If this is still a valid absolute URL, it must be external
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			throw new InvalidArgumentException(sprintf('External respository URL "%s" not allowed', $url),
				InvalidArgumentException::EXTERNAL_REPOSITORY_URL_NOT_ALLOWED);
		}

		return \Apparat\Object\Framework\isAbsoluteBareUrl($apparatBaseUrl.$url) ? $url : false;
	}
}