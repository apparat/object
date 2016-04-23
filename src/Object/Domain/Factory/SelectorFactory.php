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

namespace Apparat\Object\Domain\Factory;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Repository\InvalidArgumentException;
use Apparat\Object\Domain\Repository\Selector as RepositorySelector;

/**
 * Object selector factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class SelectorFactory
{
    /**
     * Date PCRE pattern
     *
     * @var array
     * @see ObjectUrl::$datePattern
     */
    protected static $datePattern = [
        'Y' => '/(?P<year>\d{4}|\*)',
        'm' => '(?:/(?P<month>\d{2}|\*)',
        'd' => '(?:/(?P<day>\d{2}|\*)',
        'H' => '(?:/(?P<hour>\d{2}|\*)',
        'i' => '(?:/(?P<minute>\d{2}|\*)',
        's' => '(?:/(?P<second>\d{2}|\*)',
    ];

    /**
     * Parse and instantiate an object selector
     *
     * @param string $selector String selector
     * @return \Apparat\Object\Domain\Repository\Selector Object selector
     * @throws InvalidArgumentException If the selector is invalid
     */
    public static function createFromString($selector)
    {
        $datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));
        $selectorPattern = '/(?P<id>(?:\d+)|\*)\.(?P<type>(?:[a-z]+)|\*)(?:/\\k<id>(?:-(?P<revision>\d+))?)?';

        // If the creation date is used as selector component
        if ($datePrecision) {
            $selectorPattern = implode(
                    '',
                    array_slice(
                        self::$datePattern,
                        0,
                        $datePrecision
                    )
                ).'(?:'.$selectorPattern.str_repeat(')?', $datePrecision);
        }
        $selectorPattern = '%^'.$selectorPattern.'$%';

        // If the selector is invalid
        if (!strlen($selector) ||
            !preg_match(
                $selectorPattern,
                $selector,
                $selectorParts
            ) ||
            !strlen($selectorParts[0])
        ) {
            throw new InvalidArgumentException(
                sprintf('Invalid repository selector "%s"', $selector),
                InvalidArgumentException::INVALID_REPOSITORY_SELECTOR
            );
        }

        $year = $month = $day = $hour = $minute = $second = null;
        if (($datePrecision > 0)) {
            $year = isset($selectorParts['year']) ? self::castInt(
                $selectorParts['year']
            ) : RepositorySelector::WILDCARD;
        }
        if (($datePrecision > 1)) {
            $month = isset($selectorParts['month']) ? self::castInt(
                $selectorParts['month']
            ) : RepositorySelector::WILDCARD;
        }
        if (($datePrecision > 2)) {
            $day = isset($selectorParts['day']) ? self::castInt($selectorParts['day']) : RepositorySelector::WILDCARD;
        }
        if (($datePrecision > 3)) {
            $hour = isset($selectorParts['hour']) ? self::castInt(
                $selectorParts['hour']
            ) : RepositorySelector::WILDCARD;
        }
        if (($datePrecision > 4)) {
            $minute = isset($selectorParts['minute']) ? self::castInt(
                $selectorParts['minute']
            ) : RepositorySelector::WILDCARD;
        }
        if (($datePrecision > 5)) {
            $second = isset($selectorParts['second']) ? self::castInt(
                $selectorParts['second']
            ) : RepositorySelector::WILDCARD;
        }
        $uid = isset($selectorParts['id']) ? self::castInt($selectorParts['id']) : RepositorySelector::WILDCARD;

        $type = empty($selectorParts['type']) ? RepositorySelector::WILDCARD : trim($selectorParts['type']);
        $revision = (isset($selectorParts['revision']) && strlen($selectorParts['revision'])) ? intval(
            $selectorParts['revision']
        ) : Revision::CURRENT;

        return Kernel::create(
            RepositorySelector::class,
            [$year, $month, $day, $hour, $minute, $second, $uid, $type, $revision]
        );
    }

    /**
     * Cast a value as integer if it's not a wildcard
     *
     * @param string $value Value
     * @return int|string Integer value or wildcard
     */
    protected static function castInt($value)
    {
        return ($value === RepositorySelector::WILDCARD) ? $value : intval($value);
    }
}
