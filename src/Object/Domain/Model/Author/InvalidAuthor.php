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
 * Invalid author stub
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class InvalidAuthor implements AuthorInterface
{
    /**
     * String value
     *
     * @var string
     */
    protected $value = '';
    /**
     * Underlying exception
     *
     * @var \Exception
     */
    protected $exception = null;

    /**
     * Invalid author constructor
     *
     * @param string $value Value
     */
    public function __construct($value, \Exception $exception = null)
    {
        $this->value = $value;
        $this->exception = $exception;
    }

    /**
     * Unserialize the string representation of this property
     *
     * @param string $str Serialized property
     * @return SerializablePropertyInterface Property
     */
    public static function unserialize($str)
    {
        return new static($str);
    }

    /**
     * Return the underying exception
     *
     * @return \Exception Underlying exception
     */
    public function getException()
    {
        return $this->exception;
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
        return $this->value;
    }
}
