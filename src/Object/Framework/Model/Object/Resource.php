<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framework
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

namespace Apparat\Object\Framework\Model\Object;

use Apparat\Object\Application\Factory\Properties;
use Apparat\Object\Application\Model\Object\ResourceInterface;
use Apparat\Object\Application\Model\Properties\AbstractDomainProperties;
use Apparat\Object\Application\Model\Properties\MetaProperties;
use Apparat\Object\Application\Model\Properties\SystemProperties;
use Apparat\Resource\Domain\Contract\ReaderInterface;
use Apparat\Resource\Framework\Model\Resource\FrontMarkResource;

/**
 * Object resource
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Framework
 */
class Resource extends FrontMarkResource implements ResourceInterface
{
	/**
	 * Property data
	 *
	 * @var array
	 */
	protected $_propertyData = null;
	/**
	 * System properties
	 *
	 * @var SystemProperties
	 */
	protected $_systemProperties = null;
	/**
	 * Meta properties
	 *
	 * @var MetaProperties
	 */
	protected $_metaProperties = null;
	/**
	 * Domain properties
	 *
	 * @var AbstractDomainProperties
	 */
	protected $_domainProperties = null;

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Object resource constructor
	 *
	 * @param ReaderInterface $reader Reader instance
	 */
	public function __construct(ReaderInterface $reader)
	{
		parent::__construct($reader);

		// Load the property data
		$this->_propertyData = $this->getData();
	}

	/**
	 * Get the system object properties collection
	 *
	 * @return SystemProperties System object properties collection
	 */
	public function getSystemProperties()
	{
		// Lazy-loading the system properties
		if (!$this->_systemProperties instanceof SystemProperties) {
			$this->_systemProperties = Properties::create(SystemProperties::COLLECTION, $this->_propertyData, null);
		}

		return $this->_systemProperties;
	}

	/**
	 * Set the system object properties collection
	 *
	 * @param SystemProperties $systemProperties
	 * @return ResourceInterface Object resource
	 */
	public function setSystemProperties(SystemProperties $systemProperties)
	{
		// TODO: Implement setSystemProperties() method.
	}

	/**
	 * Get the meta object properties collection
	 *
	 * @return MetaProperties Meta object properties collection
	 */
	public function getMetaProperties()
	{
		// Lazy-loading the meta properties
		if (!$this->_metaProperties instanceof MetaProperties) {
			$this->_metaProperties = Properties::create(MetaProperties::COLLECTION, $this->_propertyData, null);
		}

		return $this->_metaProperties;
	}

	/**
	 * Set the meta object properties collection
	 *
	 * @param MetaProperties $metaProperties
	 * @return ResourceInterface Object resource
	 */
	public function setMetaProperties(MetaProperties $metaProperties)
	{
		// TODO: Implement setMetaProperties() method.
	}

	/**
	 * Get the domain object properties collection
	 *
	 * @return AbstractDomainProperties Domain object properties collection
	 */
	public function getDomainProperties()
	{
		// Lazy-loading the domain properties
		if (!$this->_domainProperties instanceof AbstractDomainProperties) {
			$this->_domainProperties = Properties::create(AbstractDomainProperties::COLLECTION, $this->_propertyData,
				$this->getSystemProperties()->getProperty('type'));
		}

		return $this->_domainProperties;
	}

	/**
	 * Set the domain object properties collection
	 *
	 * @param AbstractDomainProperties $domainProperties
	 * @return ResourceInterface Object resource
	 */
	public function setDomainProperties(AbstractDomainProperties $domainProperties)
	{
		// TODO: Implement setDomainProperties() method.
	}

	/**
	 * Return the object payload
	 *
	 * @return string Object payload
	 */
	public function getPayload()
	{
		// TODO: Implement getPayload() method.
	}

	/**
	 * Set the object payload
	 *
	 * @param string $payload Object payload
	 * @return ResourceInterface Object resource
	 */
	public function setPayload($payload)
	{
		// TODO: Implement setPayload() method.
	}
}