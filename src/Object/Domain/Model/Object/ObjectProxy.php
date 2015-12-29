<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\<Layer>
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

use Apparat\Object\Domain\Model\Path\Url;

/**
 * Object proxy (lazy loading)
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain\Model\Object
 */
class ObjectProxy implements ObjectInterface
{
	/**
	 * Object URL
	 *
	 * @var Url
	 */
	protected $_url = null;
	/**
	 * Object
	 *
	 * @var ObjectInterface
	 */
	protected $_object = null;

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Constructor
	 *
	 * @param Url $url Remote object URL
	 */
	public function __construct(Url $url)
	{
		$this->_url = $url;
	}

	/*******************************************************************************
	 * MAGIG METHODS
	 *******************************************************************************/

	/**
	 * Generic caller
	 *
	 * @param string $name Method name
	 * @param array $arguments Method arguments
	 */
	public function __call($name, $arguments)
	{
		$object = $this->_object();
		if (is_callable(array($object, $name))) {
			return $object->$name(...$arguments);
		}

		throw new InvalidArgumentException(sprintf('Invalid object proxy method "%s"', $name),
			InvalidArgumentException::INVALID_OBJECT_PROXY_METHOD);
	}

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Return the enclosed remote object
	 *
	 * @return ObjectInterface Remote object
	 */
	protected function _object()
	{

		// Lazy-load the remote object if necessary
		if (!$this->_object instanceof ObjectInterface) {
			// TODO: Lazy loading
		}

		return $this->_object;
	}
}