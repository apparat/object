<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Model
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

/**
 * System object properties
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain\Model\Object
 */
class SystemProperties extends AbstractProperties
{
	/**
	 * Repository path
	 *
	 * @var RepositoryPath
	 */
	protected $_path;
	/**
	 * URL based UUID
	 *
	 * @var string
	 * @see https://de.wikipedia.org/wiki/Universally_Unique_Identifier#Namensbasierte_UUIDs_.28Version_3_und_5.29
	 */
	protected $_uuid;
	/**
	 * Object hash
	 *
	 * @var string
	 */
	protected $_hash;
	/**
	 * Object keywords
	 *
	 * @var array
	 */
	protected $_keywords = [];
	/**
	 * Object categories
	 *
	 * @var array
	 */
	protected $_categories = [];
	/**
	 * Object authors
	 *
	 * @var array
	 */
	protected $_authors = [];

	/**
	 * System properties constructor
	 *
	 * @param array $properties System properties
	 * @param RepositoryPath|null $path
	 */
	protected function __construct(array $properties, RepositoryPath $path = null)
	{
		$this->_path = $path;
	}
}