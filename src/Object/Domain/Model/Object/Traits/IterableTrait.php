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

namespace Apparat\Object\Domain\Model\Object\Traits;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\AbstractObject;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Object\Revision;

/**
 * Iterable trait
 *
 * @package Apparat\Object\Domain
 * @property Revision $currentRevision
 * @property AbstractObject $this
 */
trait IterableTrait
{
    /**
     * Return the current revision
     *
     * @return ObjectInterface Self reference
     */
    public function current()
    {
        echo "current\n";
        /** @var Revision $currentRevision */
        $currentRevision = Kernel::create(Revision::class, [$this->currentRevision->getRevision()]);
        return $this->useRevision($currentRevision);
    }

    /**
     * Forward to the next revision
     */
    public function next()
    {
        echo "next\n";
        $this->currentRevision = $this->currentRevision->increment();
    }

    /**
     * Return the current revision number
     *
     * @return int Current revision
     */
    public function key()
    {
        echo "key\n";
        return $this->currentRevision;
    }

    /**
     * Return whether the current revision is valid
     *
     * @return bool Current revision is valid
     */
    public function valid()
    {
        echo "valud\n";
        /** AbstractObject $this */
        return $this->currentRevision->getRevision() <= count($this);
    }

    /**
     * Rewind to the first revision
     */
    public function rewind()
    {
        echo "rewind\n";
        /** @var Revision $firstRevision */
        $firstRevision = Kernel::create(Revision::class, [1]);
        $this->useRevision($firstRevision);
    }

    /**
     * Return the number of available revisions
     *
     * @return int Number of available revisions
     */
    public function count()
    {
        /** AbstractObject $this */
        return $this->latestRevision->getRevision();
    }
}