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

namespace Apparat\Object\Domain\Model\Properties;

use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Abstract generic object properties collection
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
abstract class AbstractGenericProperties extends AbstractProperties implements GenericPropertiesInterface
{
	/**
	 * Property data
	 *
	 * @var array
	 */
	protected $_data = [];

	/**
	 * Property collection constructor
	 *
	 * @param array $data Property data
	 * @param ObjectInterface $object Owner object
	 */
	public function __construct(array $data, ObjectInterface $object)
	{
		parent::__construct($data, $object);

		$this->_data = $data;
	}

	/**
	 * Get a particular property value
	 *
	 * Multi-level properties might be traversed by property name paths separated with colons (":").
	 *
	 * @param string $property Property name
	 * @return mixed Property value
	 * @throws InvalidArgumentException If the property name is empty
	 */
	public function getProperty($property)
	{
		$propertyPath = array_filter(array_map('trim', explode(self::PROPERTY_TRAVERSAL_SEPARATOR, $property)));

		// If the property traversal path is empty
		if (!count($propertyPath)) {
			throw new InvalidArgumentException('Empty property name', InvalidArgumentException::EMPTY_PROPERTY_NAME);
		}

		// Traverse the property tree
		$propertyPathTraversed = [];
		$data =& $this->_data;
		foreach ($propertyPath as $property) {
			$propertyPathTraversed[] = $property;

			// If the property name step is invalid
			if (!array_key_exists($property, $data)) {
				throw new InvalidArgumentException(sprintf('Invalid property name "%s"',
					implode(self::PROPERTY_TRAVERSAL_SEPARATOR, $propertyPathTraversed)),
					InvalidArgumentException::INVALID_PROPERTY_NAME);
			}

			$data =& $data[$property];
		}

		return $data;
	}
}