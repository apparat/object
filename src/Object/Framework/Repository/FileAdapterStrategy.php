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

use Apparat\Object\Domain\Model\Object\Path;
use Apparat\Object\Domain\Model\Repository\AdapterStrategyInterface;
use Apparat\Object\Domain\Model\Repository\Selector;
use Apparat\Object\Domain\Model\Repository\SelectorInterface;

/**
 * File adapter strategy
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Framework
 */
class FileAdapterStrategy implements AdapterStrategyInterface
{
	/**
	 * Configuration
	 *
	 * Example
	 *
	 * @var array
	 */
	protected $_config = null;
	/**
	 * Root directory
	 *
	 * @var string
	 */
	protected $_root = null;
	/**
	 * Adapter strategy type
	 *
	 * @var string
	 */
	const TYPE = 'file';

	/**
	 * Adapter strategy constructor
	 *
	 * @param array $config Adapter strategy configuration
	 * @throws InvalidArgumentException If the root directory configuration is empty
	 * @throws InvalidArgumentException If the root directory configuration is invalid
	 */
	public function __construct(array $config)
	{
		$this->_config = $config;

		// If the root directory configuration is empty
		if (empty($this->_config['root'])) {
			throw new InvalidArgumentException('Empty file adapter strategy root',
				InvalidArgumentException::EMTPY_FILE_STRATEGY_ROOT);
		}

		// If the root directory configuration is invalid
		$this->_root = realpath($this->_config['root']);
		if (empty($this->_root) || !@is_dir($this->_root)) {
			throw new InvalidArgumentException(sprintf('Invalid file adapter strategy root "%s"',
				$this->_config['root']),
				InvalidArgumentException::INVALID_FILE_STRATEGY_ROOT);
		}
	}

	/**
	 * Return the adapter strategy type
	 *
	 * @return string Adapter strategy type
	 */
	public function getType()
	{
		return self::TYPE;
	}

	/**
	 * Find objects by selector
	 *
	 * @param Selector $selector Object selector
	 * @return array[Path] Object paths
	 */
	public function findObjectPaths(SelectorInterface $selector)
	{
		chdir($this->_root);

		// Build a glob string from the selector
		$glob = '';
		$globFlags = GLOB_ONLYDIR | GLOB_NOSORT;

		$year = $selector->getYear();
		if ($year !== null) {
			$glob .= '/'.$year;
		}

		$month = $selector->getMonth();
		if ($month !== null) {
			$glob .= '/'.$month;
		}

		$day = $selector->getDay();
		if ($day !== null) {
			$glob .= '/'.$day;
		}

		$hour = $selector->getHour();
		if ($hour !== null) {
			$glob .= '/'.$hour;
		}

		$minute = $selector->getMinute();
		if ($minute !== null) {
			$glob .= '/'.$minute;
		}

		$second = $selector->getSecond();
		if ($second !== null) {
			$glob .= '/'.$second;
		}

		$id = $selector->getId();
		$type = $selector->getType();
		if (($id !== null) || ($type !== null)) {
			$glob .= '/'.($id ?: Selector::WILDCARD).'.'.($type ?: Selector::WILDCARD);

			$revision = $selector->getRevision();
			if ($revision !== null) {
				$glob .= '/'.($id ?: Selector::WILDCARD).'-'.$revision;
				$globFlags &= ~GLOB_ONLYDIR;
			}
		}

		return array_map(function($objectPath) {
			return new Path('/'.$objectPath);
		}, glob(ltrim($glob, '/'), $globFlags));
	}
}