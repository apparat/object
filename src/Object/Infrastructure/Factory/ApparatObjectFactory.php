<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Infrastructure\Factory
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

namespace Apparat\Object\Infrastructure\Factory;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Contract\ObjectTypesInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Ports\Contract\ApparatObjectInterface;
use Apparat\Object\Ports\Exceptions\InvalidArgumentException;
use Apparat\Object\Ports\Object\Article;

/**
 * Apparat object factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 */
class ApparatObjectFactory implements ObjectTypesInterface
{
    /**
     * Type classes
     *
     * @var array
     */
    protected static $typeClasses = [
        self::ARTICLE => Article::class,
        self::AUDIO => false,
        self::BOOKMARK => false,
        self::CHECKIN => false,
        self::CITE => false,
        self::CODE => false,
        self::CONTACT => false,
        self::ADDRESS => false,
        self::EVENT => false,
        self::FAVOURITE => false,
        self::GEO => false,
        self::IMAGE => false,
        self::ITEM => false,
        self::LIKE => false,
        self::NOTE => false,
        self::PROJECT => false,
        self::REPLY => false,
        self::REVIEW => false,
        self::RSVP => false,
        self::VENUE => false,
        self::VIDEO => false,
    ];

    /**
     * Create and return an apparat object decorator
     *
     * @param ObjectInterface $object Object
     * @return ApparatObjectInterface Apparat object
     */
    public static function create(ObjectInterface $object)
    {
        $objectType = $object->getType()->getType();

        // If the object type doesn't map to known apparat object class
        if (!array_key_exists($objectType, self::$typeClasses) || !self::$typeClasses[$objectType]) {
            throw new InvalidArgumentException(
                sprintf('Unknown apparat object type "%s"', $objectType),
                InvalidArgumentException::UNKNOWN_APPARAT_OBJECT_TYPE
            );
        }

        return Kernel::create(self::$typeClasses[$objectType], [$object]);
    }
}
