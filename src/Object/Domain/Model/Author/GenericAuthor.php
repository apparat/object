<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT The MIT License (MIT)
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
    private $name;
    /**
     * Email address
     *
     * @var string
     */
    private $email;
    /**
     * URL
     *
     * @var string
     */
    private $url;

    /**
     * Generic author constructor
     *
     * @param string $name Name
     * @param string $email Email address
     * @param string $url URL
     */
    public function __construct($name, $email = null, $url = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->url = $url;
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
        if (!strlen(trim($str)) || !preg_match("%^([^\<]+)?(?:\s+\<([^\>]+)\>)?(?:\s+\(([^\)]+)\))?$%", $str,
                $author)
        ) {
            throw new InvalidArgumentException(
                sprintf('Invalid generic author "%s"', $str),
                InvalidArgumentException::INVALID_GENERIC_AUTHOR
            );
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

    /**
     * Serialize the property
     *
     * @return mixed Property serialization
     */
    public function serialize()
    {
        $parts = [$this->name];

        if (strlen($this->email)) {
            $parts[] = '<'.$this->email.'>';
        }

        if (strlen($this->url)) {
            $parts[] = '('.$this->url.')';
        }

        return implode(' ', $parts);
    }
}
