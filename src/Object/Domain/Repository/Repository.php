<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Repository;

use Apparat\Object\Domain\Common\SingletonTrait;
use Apparat\Object\Domain\Model\Object\Collection;
use Apparat\Object\Domain\Model\Object\FactoryInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\RepositoryPath;

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
	 * Adapter strategy
	 *
	 * @var AdapterStrategyInterface
	 */
	protected $_adapterStrategy = null;
	/**
	 * Object factory
	 *
	 * @var FactoryInterface
	 */
	protected $_objectFactory = null;
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
	 * @param AdapterStrategyInterface $adapterStrategy Repository adapter strategy
	 * @param FactoryInterface $objectFactory Object factory
	 * @return Repository Repository instance
	 */
	public static function create(AdapterStrategyInterface $adapterStrategy, FactoryInterface $objectFactory)
	{
		$signature = $adapterStrategy->getSignature().$objectFactory->getSignature();
		if (empty(self::$_instances[$signature])) {
			self::$_instances[$signature] = new static($adapterStrategy, $objectFactory);
		}

		return self::$_instances[$signature];
	}

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Find objects by selector
	 *
	 * @param SelectorInterface $selector Object selector
	 * @return Collection Object collection
	 */
	public function findObjects(SelectorInterface $selector)
	{
		return new Collection($this->_adapterStrategy->findObjectPaths($selector, $this));
	}

	/**
	 * Add an object to the repository
	 *
	 * @param ObjectInterface $object Object
	 * @return boolean Success
	 */
	public function addObject(ObjectInterface $object)
	{
		// TODO: Implement addObject() method.
	}

	/**
	 * Delete and object from the repository
	 *
	 * @param ObjectInterface $object Object
	 * @return boolean Success
	 */
	public function deleteObject(ObjectInterface $object)
	{
		// TODO: Implement deleteObject() method.
	}

	/**
	 * Update an object in the repository
	 *
	 * @param ObjectInterface $object Object
	 * @return bool Success
	 */
	public function updateObject(ObjectInterface $object)
	{
		// TODO: Implement updateObject() method.
	}

	/**
	 * Load an object from this repository
	 *
	 * @param RepositoryPath $path Repository object path
	 * @return ObjectInterface Object
	 */
	public function loadObject(RepositoryPath $path)
	{
		// TODO: Really OK to cache? (Immutability ...)
		if (empty($this->_objectCache[$path->getId()->getId()])) {
			$this->_objectCache[$path->getId()->getId()] = $this->_objectFactory->loadObject($path);
		}

		return $this->_objectCache[$path->getId()->getId()];
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Repository constructor
	 *
	 * @param AdapterStrategyInterface $adapterStrategy Repository adapter strategy
	 * @param FactoryInterface $objectFactory Object factory
	 */
	protected function __construct(AdapterStrategyInterface $adapterStrategy, FactoryInterface $objectFactory)
	{
		$this->_adapterStrategy = $adapterStrategy;
		$this->_objectFactory = $objectFactory;
	}
}