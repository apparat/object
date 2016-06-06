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

use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Payload trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
trait PayloadTrait
{
    /**
     * Object payload
     *
     * @var string
     */
    protected $payload;


    /**
     * Return the object payload
     *
     * @return string Object payload
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set the payload
     *
     * @param string $payload Payload
     * @return ObjectInterface Self reference
     */
    public function setPayload($payload)
    {
        // If the payload is changed
        if ($payload !== $this->payload) {
            $this->setMutatedState();
        }

        $this->payload = $payload;

        /** @var ObjectInterface $this */
        return $this;
    }

    /**
     * Set the object state to mutated
     */
    abstract protected function setMutatedState();
}
