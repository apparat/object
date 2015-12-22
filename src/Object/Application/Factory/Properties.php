<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
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

namespace Apparat\Object\Application\Factory;

use Apparat\Object\Application\Model\Properties\AbstractDomainProperties;
use Apparat\Object\Application\Model\Properties\InvalidArgumentException;
use Apparat\Object\Application\Model\Properties\MetaProperties;
use Apparat\Object\Application\Model\Properties\SystemProperties;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * Property collection factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class Properties
{

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Create a properties collection from data
	 *
	 * @param string $collection Collection name
	 * @param array $data Property data
	 * @param string|NULL Object type
	 * @throws InvalidArgumentException If the collection name is empty
	 * @throws InvalidArgumentException If the collection name is invalid
	 */
	public static function create($collection, array $data, $objectType)
	{

		// If the collection name is empty
		if (!strlen($collection)) {
			throw new InvalidArgumentException('Empty property collection name',
				InvalidArgumentException::EMPTY_COLLECTION_NAME);
		}

		$factoryMethod = '_create'.ucfirst($collection).'Properties';
		if (!is_callable("static::$factoryMethod")) {
			throw new InvalidArgumentException(sprintf('Invalid property collection name "%s"', $collection),
				InvalidArgumentException::INVALID_COLLECTION_NAME);
		}

		return call_user_func("static::$factoryMethod", $data, $objectType);
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Create the system property collection
	 *
	 * @param array $data Property data
	 * @return SystemProperties System property collection
	 */
	protected static function _createSystemProperties(array $data)
	{
		$systemPropertyData = (empty($data[SystemProperties::COLLECTION]) || !is_array($data[SystemProperties::COLLECTION])) ? [] : $data[SystemProperties::COLLECTION];
		return new SystemProperties($systemPropertyData);
	}

	/**
	 * Create the meta property collection
	 *
	 * @param array $data Property data
	 * @return MetaProperties Meta property collection
	 */
	protected static function _createMetaProperties(array $data)
	{
		$metaPropertyData = (empty($data[MetaProperties::COLLECTION]) || !is_array($data[MetaProperties::COLLECTION])) ? [] : $data[MetaProperties::COLLECTION];
		return new MetaProperties($metaPropertyData);
	}

	/**
	 * Create the object type dependent domain property collection
	 *
	 * @param array $data Property data
	 * @return AbstractDomainProperties Domain property collection
	 */
	protected static function _createDomainProperties(array $data, $objectType)
	{
		// If the object type is unknown
		$domainPropertyCollectionClass = 'Apparat\\Object\\Application\\Model\\Properties\\Domain\\'.ucfirst($objectType);
		if (!Type::isValidType($objectType) || !class_exists($domainPropertyCollectionClass)) {
			throw new InvalidArgumentException(sprintf('Invalid object type "%s"', $objectType),
				InvalidArgumentException::INVALID_OBJECT_TYPE);
		}

		$domainPropertyData = (empty($data[AbstractDomainProperties::COLLECTION]) || !is_array($data[AbstractDomainProperties::COLLECTION])) ? [] : $data[AbstractDomainProperties::COLLECTION];
		return new $domainPropertyCollectionClass($domainPropertyData);
	}
}