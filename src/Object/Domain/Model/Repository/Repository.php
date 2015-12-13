<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat_<Package>
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

namespace Apparat\Object\Domain\Model\Repository;

use Apparat\Object\Domain\Model\Object\Collection;
use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Abstract object repository
 *
 * @package Apparat\Object\Domain\Model\Repository
 */
class Repository implements RepositoryInterface
{
	/**
	 * Adapter strategy
	 *
	 * @var AdapterStrategyInterface
	 */
	protected $_adapterStrategy = null;

	/**
	 * Repository constructor
	 *
	 * @param AdapterStrategyInterface $adapterStrategy Repository adapter strategy
	 */
	public function __construct(AdapterStrategyInterface $adapterStrategy)
	{
		$this->_adapterStrategy = $adapterStrategy;
	}

	/**
	 * Find objects by selector
	 *
	 * @param SelectorInterface $selector Object selector
	 * @return Collection Object collection
	 */
	public function findObjects(SelectorInterface $selector)
	{
		$objectPaths = $this->_adapterStrategy->findObjectPaths($selector);
		return $objectPaths;
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
}