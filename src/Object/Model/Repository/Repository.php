<?php

/**
 * apparat-object
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

namespace Apparat\Object\Model\Repository;

use Apparat\Object\Model\Object\Object;
use Apparat\Object\Model\Object\ObjectCollection;

/**
 * Object repository interface
 *
 * @package Apparat\Object\Model\Repository
 */
interface Repository
{
	/**
	 * Find objects by selector
	 *
	 * @param $selector Object selector
	 * @return ObjectCollection Object collection
	 */
	public function findObjects($selector);

	/**
	 * Add an object to the repository
	 *
	 * @param Object $object Object
	 * @return boolean Success
	 */
	public function addObject(Object $object);

	/**
	 * Delete and object from the repository
	 *
	 * @param Object $object Object
	 * @return boolean Success
	 */
	public function deleteObject(Object $object);

	/**
	 * Update an object in the repository
	 *
	 * @param Object $object Object
	 * @return bool Success
	 */
	public function updateObject(Object $object);
}