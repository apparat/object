<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Apparat\Server\Object
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

namespace Apparat\Object\Domain\Model\Object\Traits;

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\Revision;

/**
 * Iterable trait for object proxy
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
trait IterableProxyTrait
{
    /**
     * Return the current revision
     *
     * @return ObjectInterface Self reference
     */
    public function current()
    {
        return $this->object()->current();
    }

    /**
     * Forward to the next revision
     */
    public function next()
    {
        $this->object()->next();
    }

    /**
     * Return the current revision number
     *
     * @return Revision Current (next) revision
     */
    public function key()
    {
        return $this->object()->key();
    }

    /**
     * Return whether the current revision is valid
     *
     * @return bool Current revision is valid
     */
    public function valid()
    {
        return $this->object()->valid();
    }

    /**
     * Rewind to the first revision
     */
    public function rewind()
    {
        $this->object()->rewind();
    }

    /**
     * Return the number of available revisions
     *
     * @return int Number of available revisions
     */
    public function count()
    {
        return $this->object()->count();
    }

    /**
     * Return the enclosed remote object
     *
     * @return ObjectInterface Remote object
     */
    abstract protected function object();
}
