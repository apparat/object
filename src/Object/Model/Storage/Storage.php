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

namespace Apparat\Object\Model\Storage;

use Apparat\Object\Model\Object\Object;
use Apparat\Object\Model\Object\ObjectCollection;

/**
 * Object storage interface
 *
 * @package Apparat\Object\Model\Storage
 */
interface Storage
{
	/**
	 * Find objects by selector
	 *
	 * @param $objectSelector Object selector
	 * @return ObjectCollection Object collection
	 */
	public function findObjects($objectSelector);

	/**
	 * Add an object to the storage
	 *
	 * @param Object $object Object
	 * @return boolean Success
	 */
	public function addObject(Object $object);

	/**
	 * Delete and object from the storage
	 *
	 * @param Object $object Object
	 * @return boolean Success
	 */
	public function deleteObject(Object $object);

	/**
	 * Update an object in the storage
	 *
	 * @param Object $object Object
	 * @return boolean Success
	 */
	public function updateUbject(Object $object);
}