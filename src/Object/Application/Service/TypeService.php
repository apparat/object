<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Server
 * @subpackage  Apparat\Object\Application\Service
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

namespace Apparat\Object\Application\Service;

use Apparat\Object\Application\Contract\ObjectTypesInterface;
use Apparat\Object\Domain\Contract\TypeServiceInterface;

/**
 * Type service
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class TypeService implements TypeServiceInterface, ObjectTypesInterface
{
    /**
     * Enabled types
     *
     * @var array
     */
    protected static $enabledTypes = [
        self::ARTICLE => false,
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
     * Return whether a particular object type is supported
     *
     * Non-static variant for domain usage
     *
     * @param string $type Object type
     * @return boolean Object type is supported
     */
    public function supportsType($type)
    {
        return self::isEnabled($type);
    }

    /**
     * Enable an object type
     *
     * @param $type
     * @throws OutOfBoundsException If the object type is invalid
     */
    public static function enableType($type)
    {
        self::$enabledTypes[self::validateType($type)] = true;
    }

    /**
     * Return whether a particular object type is enabled
     *
     * @param string $type Object type
     * @return boolean Object type is enabled
     */
    public static function isEnabled($type)
    {
        try {
            return self::$enabledTypes[self::validateType($type)];
        } catch (OutOfBoundsException $e) {
            return false;
        }
    }

    /**
     * Return all object types that are currently supported
     *
     * @return array Supported object types
     */
    public static function getSupportedTypes()
    {
        $supportedTypes = array_keys(array_filter(self::$enabledTypes));
        return array_combine($supportedTypes, $supportedTypes);
    }

    /**
     * Validate a type
     *
     * @param string $type Object type
     * @return string Validated object type
     * @throws OutOfBoundsException If the object type is invalid
     */
    protected static function validateType($type)
    {
        $type = trim($type);

        // If the object type is invalid
        if (!strlen($type) || !array_key_exists($type, self::$enabledTypes)) {
            throw new OutOfBoundsException(
                sprintf('Invalid object type "%s"', $type),
                OutOfBoundsException::INVALID_OBJECT_TYPE
            );
        }

        return $type;
    }
}