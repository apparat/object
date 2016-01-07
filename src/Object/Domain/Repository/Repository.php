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

namespace Apparat\Object\Domain\Repository;

use Apparat\Object\Domain\Common\SingletonTrait;
use Apparat\Object\Domain\Model\Object\Collection;
use Apparat\Object\Domain\Model\Object\ManagerInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Path\PathInterface;
use Apparat\Object\Domain\Model\Path\RepositoryPath;

/**
 * Abstract object repository
 *
 * @package Apparat\Object\Domain\Repository
 */
class Repository implements RepositoryInterface
{
	/**
	 * Use singleton methods
	 */
	use SingletonTrait;

	/**
	 * Apparat base URL
	 *
	 * @var string
	 */
	protected $_url = null;
	/**
	 * Adapter strategy
	 *
	 * @var AdapterStrategyInterface
	 */
	protected $_adapterStrategy = null;
	/**
	 * Object factory
	 *
	 * @var ManagerInterface
	 */
	protected $_objectManager = null;
	/**
	 * Instance specific object cache
	 *
	 * @var array
	 */
	protected $_objectCache = [];

	/**
	 * Singleton instances
	 *
	 * @var Repository[]
	 */
	protected static $_instances = [];

	/*******************************************************************************
	 * STATIC METHODS
	 *******************************************************************************/

	/**
	 * Repository singleton instantiator
	 *
	 * @param string $url Apparat base URL
	 * @param AdapterStrategyInterface $adapterStrategy Repository adapter strategy
	 * @param ManagerInterface $objectManager Object manager
	 * @return Repository Repository instance
	 */
	public static function instance($url, AdapterStrategyInterface $adapterStrategy, ManagerInterface $objectManager)
	{
		$signature = $url.$adapterStrategy->getSignature().$objectManager->getSignature();
		if (empty(self::$_instances[$signature])) {
			self::$_instances[$signature] = new static($url, $adapterStrategy, $objectManager);
		}

		return self::$_instances[$signature];
	}

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * @inheritDoc
	 */
	public function findObjects(SelectorInterface $selector)
	{
		return new Collection($this->_adapterStrategy->findObjectPaths($selector, $this));
	}

	/**
	 * @inheritDoc
	 */
	public function addObject(ObjectInterface $object)
	{
		// TODO: Implement addObject() method.
	}

	/**
	 * @inheritDoc
	 */
	public function deleteObject(ObjectInterface $object)
	{
		// TODO: Implement deleteObject() method.
	}

	/**
	 * @inheritDoc
	 */
	public function updateObject(ObjectInterface $object)
	{
		// TODO: Implement updateObject() method.
	}

	/**
	 * Load an object from this repository
	 *
	 * @param PathInterface $path Object path
	 * @return ObjectInterface Object
	 */
	public function loadObject(PathInterface $path)
	{
		// TODO: Really OK to cache? (Immutability ...)
		if (empty($this->_objectCache[$path->getId()->getId()])) {
			$this->_objectCache[$path->getId()->getId()] = $this->_objectManager->loadObject(new RepositoryPath($this,
				$path));
		}

		return $this->_objectCache[$path->getId()->getId()];
	}

	/**
	 * @inheritDoc
	 */
	public function getAdapterStrategy()
	{
		return $this->_adapterStrategy;
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Repository constructor
	 *
	 * @param string $url Apparat base URL
	 * @param AdapterStrategyInterface $adapterStrategy Repository adapter strategy
	 * @param ManagerInterface $objectManager Object factory
	 * @throws InvalidArgumentException If the apparat base URL isn't valid
	 */
	protected function __construct($url, AdapterStrategyInterface $adapterStrategy, ManagerInterface $objectManager)
	{
		$this->_url = $url;
		$this->_adapterStrategy = $adapterStrategy;
		$this->_objectManager = $objectManager;
	}
}