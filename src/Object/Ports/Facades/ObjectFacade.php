<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Ports
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

namespace Apparat\Object\Ports\Facades;

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Infrastructure\Model\Object\Object;
use Apparat\Object\Ports\Types\Object as ObjectTypes;

/**
 * Object facade
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Ports
 */
class ObjectFacade implements FacadeInterface
{
    /**
     * Object
     *
     * @var ObjectInterface
     */
    protected $object;

    /**
     * Object facade constructor
     *
     * @param ObjectInterface $object Object
     * @internal
     */
    protected function __construct(ObjectInterface $object)
    {
        $this->object = $object;
    }

    /**
     * Instantiate and return an object
     *
     * @param string $url Object URL (relative or absolute including the apparat base URL)
     * @param int $visibility Object visibility
     * @return ObjectInterface Object
     * @api
     */
    public static function load($url, $visibility = ObjectTypes::VISIBILITY_ALL)
    {
        return new static(Object::load($url, $visibility));
    }
}