<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Model\Object;

use Apparat\Object\Domain\Model\Properties\AbstractDomainProperties;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Model\Properties\ProcessingInstructions;
use Apparat\Object\Domain\Model\Properties\Relations;
use Apparat\Object\Domain\Model\Properties\SystemProperties;

/**
 * Abstract object
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
abstract class AbstractObject implements ObjectInterface
{
	/**
	 * System properties
	 *
	 * @var SystemProperties
	 */
	protected $_systemProperties;
	/**
	 * Meta properties
	 *
	 * @var MetaProperties
	 */
	protected $_metaProperties;
	/**
	 * Domain properties
	 *
	 * @var AbstractDomainProperties
	 */
	protected $_domainProperties;
	/**
	 * Object relations
	 *
	 * @var Relations
	 */
	private $_relations;
	/**
	 * Processing instructions
	 *
	 * @var ProcessingInstructions
	 */
	private $_processingInstructions;
	/**
	 * Object payload
	 *
	 * @var string
	 */
	protected $_payload;
	/**
	 * Repository path
	 *
	 * @var RepositoryPath
	 */
	protected $_path;

	/**
	 * Object constructor
	 *
	 * @param SystemProperties $systemProperties System properties
	 * @param MetaProperties $metaProperties Meta properties
	 * @param AbstractDomainProperties $domainProperties Domain properties
	 * @param Relations $relations Object relations
	 * @param ProcessingInstructions $processingInstructions Processing instructions
	 * @param string $payload Object payload
	 * @param RepositoryPath $path Object repository path
	 */
	public function __construct(
		SystemProperties $systemProperties,
		MetaProperties $metaProperties,
		AbstractDomainProperties $domainProperties,
		Relations $relations,
		ProcessingInstructions $processingInstructions,
		$payload = '',
		RepositoryPath $path
	) {
		$this->_systemProperties = $systemProperties;
		$this->_metaProperties = $metaProperties;
		$this->_domainProperties = $domainProperties;
		$this->_relations = $relations;
		$this->_processingInstructions = $processingInstructions;
		$this->_payload = $payload;
		$this->_path = $path;
	}
}