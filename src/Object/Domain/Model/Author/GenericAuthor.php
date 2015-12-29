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

namespace Apparat\Object\Domain\Model\Author;

use Apparat\Object\Domain\Contract\SerializablePropertyInterface;

/**
 * Generic author
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class GenericAuthor implements AuthorInterface
{
	/**
	 * Name
	 *
	 * @var string
	 */
	private $_name;
	/**
	 * Email address
	 *
	 * @var string
	 */
	private $_email;
	/**
	 * URL
	 *
	 * @var string
	 */
	private $_url;

	/**
	 * Generic author constructor
	 *
	 * @param string $name Name
	 * @param string $email Email address
	 * @param string $url URL
	 */
	public function __construct($name, $email = null, $url = null)
	{
		$this->_name = $name;
		$this->_email = $email;
		$this->_url = $url;
	}

	/**
	 * Serialize the property
	 *
	 * @return mixed Property serialization
	 */
	public function serialize()
	{
		$parts = [$this->_name];

		if (strlen($this->_email)) {
			$parts[] = '<'.$this->_email.'>';
		}

		if (strlen($this->_url)) {
			$parts[] = '('.$this->_url.')';
		}

		return implode(' ', $parts);
	}

	/**
	 * Unserialize the string representation of this property
	 *
	 * @param string $str Serialized property
	 * @return SerializablePropertyInterface Property
	 * @throws InvalidArgumentException If the generic author is invalid
	 */
	public static function unserialize($str)
	{
		// If the author serialization is invalid
		if (!preg_match("%^([^\<]+)(?:\s\<([^\>]+)\>)?(?:\s\(([^\)]+)\))?$%", $str, $author)) {
			throw new InvalidArgumentException(sprintf('Invalid generic author "%s"', $str),
				InvalidArgumentException::INVALID_GENERIC_AUTHOR);
		}

		$author = array_pad($author, 4, null);
		return new static($author[1], $author[2], $author[3]);
	}

	/**
	 * Return a signature uniquely representing this author
	 *
	 * @return string Author signature
	 */
	public function getSignature()
	{
		return sha1($this->serialize());
	}
}