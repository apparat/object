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

namespace Apparat\Object\Domain\Repository;

use Apparat\Object\Domain\Model\Path\ApparatUrl;
use Apparat\Object\Domain\Model\Path\ObjectUrl;

/**
 * Repository register
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Register
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
	 * @param string|ObjectUrl $url Repository URL
	 * @param RepositoryInterface $repository Repository
	 * @api
	 */
	public static function register($url, RepositoryInterface $repository)
	{
		// Repository registration
		$repositoryUrl = self::normalizeRepositoryUrl($url);

		if (!strlen($repositoryUrl)) {
			try {
				throw  new \Exception;
			} catch(\Exception $e) {
				echo $e->getTraceAsString();
			}
		}

		self::$_registry[$repositoryUrl] = $repository;
	}

	/**
	 * Instantiate and return an object repository
	 *
	 * @param string|ObjectUrl $url Repository URL (relative or absolute including the apparat base URL)
	 * @return \Apparat\Object\Domain\Repository\Repository Object repository
	 * @throws InvalidArgumentException If the repository URL is invalid
	 * @throws InvalidArgumentException If the repository URL is unknown
	 * @api
	 */
	public static function instance($url)
	{
		$url = self::normalizeRepositoryUrl($url);

		// If the local repository URL is unknown
		if (empty(self::$_registry[$url])) {
			throw new InvalidArgumentException(sprintf('Unknown repository URL "%s"', $url),
				InvalidArgumentException::UNKNOWN_REPOSITORY_URL);
		}

		// Return the repository instance
		return self::$_registry[$url];
	}

	/**
	 * Test whether a repository URL is registered
	 *
	 * @param string $url Repository URL
	 * @return bool Repository URL is registered
	 */
	public static function isRegistered($url)
	{
		return array_key_exists(self::normalizeRepositoryUrl($url), self::$_registry);
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Normalize a repository URL
	 *
	 * @param string|ObjectUrl $url Repository URL
	 * @return string Normalized repository URL
	 * @throws InvalidArgumentException If the repository URL is invalid
	 */
	public static function normalizeRepositoryUrl($url)
	{
		// If it's an apparat URL
		if ($url instanceof ApparatUrl) {
			$url = $url->getNormalizedRepositoryUrl();

			// Else: If it's an object URL
		} elseif ($url instanceof ObjectUrl) {
			$url = $url->getRepositoryUrl();

			// Else: If it's an empty URL
		} elseif ($url === null) {
			return '';
		}

		// If the URL is a string
		if (is_string($url)) {

			// Strip the leading apparat base URL
			$apparatBaseUrl = getenv('APPARAT_BASE_URL');
			if (strpos($url, $apparatBaseUrl) === 0) {
				$url = strval(substr($url, strlen($apparatBaseUrl)));
			}

			// Ensure this is a bare URL (without query and fragment)
			if (\Apparat\Object\Domain\isAbsoluteBareUrl($apparatBaseUrl.$url)) {
				return ltrim($url, '/');
			}
		}

		// The URL is invalid, throw an error
		throw new InvalidArgumentException(sprintf('Invalid repository URL "%s"', $url),
			InvalidArgumentException::INVALID_REPOSITORY_URL);
	}
}