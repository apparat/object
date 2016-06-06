<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
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

namespace Apparat\Object\Application\Utility;

/**
 * Array utility
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class ArrayUtility
{
    /**
     * Sort an array recursively by key
     *
     * @param array $array Array
     * @return array Sorted array
     */
    public static function sortRecursiveByKey(array $array)
    {
        ksort($array, SORT_REGULAR);
        while (list($key, $value) = each($array)) {
            if (is_array($value)) {
                $array[$key] = self::sortRecursiveByKey($value);
            }
        }
        return $array;
    }

    /**
     * Reduce a value to a checksum
     *
     * @param mixed $value Value
     * @return string Checksum
     */
    protected static function reduceValue($value)
    {
        $type = empty($value) ? 'empty' : gettype($value);
        if (is_array($value)) {
            return self::reduce($value);
        } elseif (is_object($value)) {
            $value = spl_object_hash($value);
        }
        return $type.'-'.sha1(strval($value));
    }

    /**
     * Return a comparable checksum of an array
     *
     * @param array $array Array
     * @return string Comparable checksum
     */
    public static function reduce(array $array)
    {
        if (self::isNumericArray($array)) {
            return self::reduceNumeric($array);
        }

        return self::reduceAssociative($array);
    }

    /**
     * Determine if all keys of an array are numeric
     *
     * @param array $array Array
     * @return bool All keys are numeric
     */
    public static function isNumericArray(array $array)
    {
        $allNumeric = true;
        foreach (array_keys($array) as $key) {
            if (!is_numeric($key)) {
                $allNumeric = false;
                break;
            }
        }
        return $allNumeric;
    }

    /**
     * Return a comparable checksum of a numeric array
     *
     * @param array $array Numeric array
     * @return string Comparable checksum
     */
    protected static function reduceNumeric(array $array)
    {
        $array = array_map(['self', 'reduceValue'], $array);
        sort($array, SORT_STRING);
        return 'array-'.sha1(serialize($array));
    }

    /**
     * Return a comparable checksum of an associative array
     *
     * @param array $array Associative array
     * @return string Comparable checksum
     */
    protected static function reduceAssociative(array $array)
    {
        ksort($array, SORT_STRING);
        $array = array_map(['self', 'reduceValue'], $array);
        return 'array-'.sha1(serialize($array));
    }
}
