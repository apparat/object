<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
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

namespace Apparat\Object\Application\Factory;

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Properties\SystemProperties;

/**
 * Object factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class ObjectFactory
{
	/**
	 * Create an object
	 *
	 * @param ResourceInterface $objectResource
	 * @param RepositoryPath $path Repository object path
	 * @return ObjectInterface Object
	 * @throws InvalidArgumentException If the object type is undefined
	 * @throws InvalidArgumentException If the object type is invalid
	 */
	public static function createFromResource(ResourceInterface $objectResource, RepositoryPath $path)
	{
		$propertyData = $objectResource->getPropertyData();

		// If the object type is undefined
		if (
			!array_key_exists(SystemProperties::COLLECTION, $propertyData) ||
			!is_array($propertyData[SystemProperties::COLLECTION]) ||
			empty($propertyData[SystemProperties::COLLECTION]['type'])
		) {
			throw new InvalidArgumentException('Undefined object type',
				InvalidArgumentException::UNDEFINED_OBJECT_TYPE);
		}

		// If the object type is invalid
		$objectType = $path->getType()->getType();
		$objectClass = 'Apparat\\Object\\Application\\Model\\Object\\'.ucfirst($objectType);
		if (!Type::isValidType($objectType) || !class_exists($objectClass)) {
			throw new InvalidArgumentException(sprintf('Invalid object type "%s"', $objectType),
				InvalidArgumentException::INVALID_OBJECT_TYPE);
		}

		// Instantiate the object
		return new $objectClass($path, $propertyData, $objectResource->getPayload());
	}
}