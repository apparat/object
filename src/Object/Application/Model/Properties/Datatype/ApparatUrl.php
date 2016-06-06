<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
 * @author      Joschi Kuphal <joschi@tollwerk.de> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@tollwerk.de> / @jkphl
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

namespace Apparat\Object\Application\Model\Properties\Datatype;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Application\Model\Properties\Domain\DomainException;

/**
 * Apparat URL
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class ApparatUrl extends Url
{
    /**
     * Match a value against this datatype
     *
     * @param mixed $value Value
     * @return mixed Matched and processed value
     * @throws DomainException If the value is not a valid URL
     */
    public function match($value)
    {
        try {
            /** @var \Apparat\Object\Domain\Model\Path\ApparatUrl $apparatUrl */
            $apparatUrl = Kernel::create(
                \Apparat\Object\Domain\Model\Path\ApparatUrl::class,
                [$value, true, $this->object->getRepositoryPath()->getRepository()]
            );

            // If the apparat URL needs to be filtered
            if (count($this->filter) && !in_array($apparatUrl->getType()->getType(), $this->filter)) {
                throw new DomainException;
            }
        } catch (DomainException $e) {
            throw new \InvalidArgumentException;

        } catch (\Exception $e) {
            throw new DomainException;
        }

        return $apparatUrl;
    }
}