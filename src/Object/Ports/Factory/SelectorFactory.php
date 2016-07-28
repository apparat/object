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

namespace Apparat\Object\Ports\Factory;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Repository\InvalidArgumentException;
use Apparat\Object\Ports\Repository\Selector;
use Apparat\Object\Ports\Repository\SelectorInterface;

/**
 * Object selector factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Ports
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
     * @return SelectorInterface Object selector
     * @throws InvalidArgumentException If the selector is invalid
     */
    public static function createFromString($selector)
    {
        // If the selector is invalid
        if (!strlen($selector) ||
            !preg_match(
                '%^'.self::getSelectorRegex(intval(getenv('OBJECT_DATE_PRECISION'))).'$%',
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

        return self::createFromParams($selectorParts);
    }

    /**
     * Return a regular expression for selector parsing
     *
     * @param int $datePrecision Object date precision
     * @return string Selector parsing regular expression
     */
    public static function getSelectorRegex($datePrecision)
    {
        $bothIndicator = preg_quote(SelectorInterface::INDICATOR_BOTH);
        $hiddenIndicator = preg_quote(SelectorInterface::INDICATOR_HIDDEN);
        $wildcard = preg_quote(SelectorInterface::WILDCARD);

        $revisionPart = '(?:-(?P<revision>(?:\d+)|'.$wildcard.'))?';

        $draftIndicator = preg_quote(SelectorInterface::INDICATOR_DRAFT);
        $draftPart = '(?P<draft>'.$bothIndicator.'|'.$draftIndicator.')?';
        $instancePart = '(?:/'.$draftPart.'(?:(?:\\k<id>)|'.$wildcard.')'.$revisionPart.')?';

        $typePart = '(?:(?:\-(?:(?P<type>(?:[a-z]+)|'.$wildcard.')))?'.$instancePart.')?';

        $hiddenPart = '(?P<visibility>'.$bothIndicator.'|'.$hiddenIndicator.')?';
        $idPart = '(?P<id>(?:\d+|'.$wildcard.'))';
        $selectorPattern = '/'.$hiddenPart.$idPart.$typePart;

        // If the creation date is used as selector component
        if ($datePrecision) {
            $selectorPattern = '(?:'.$selectorPattern.str_repeat(')?', $datePrecision);
            $selectorPattern = implode('', array_slice(self::$datePattern, 0, $datePrecision)).$selectorPattern;
        }
        return $selectorPattern;
    }

    /**
     * Instantiate an object selector from a list of parameters
     *
     * @param array $params Object selector parameters
     * @return SelectorInterface Object selector
     */
    public static function createFromParams(array $params)
    {
        $datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));

        // Object visibility
        $visibility = empty($params['visibility']) ? SelectorInterface::VISIBLE
            : (($params['visibility'] == SelectorInterface::INDICATOR_HIDDEN) ?
                SelectorInterface::HIDDEN : SelectorInterface::ALL);

        // Object draft
        $draft = empty($params['draft']) ? SelectorInterface::PUBLISHED
            : (($params['draft'] == SelectorInterface::INDICATOR_DRAFT) ?
                SelectorInterface::DRAFT : SelectorInterface::ALL);

        $year = $month = $day = $hour = $minute = $second = null;
        if (($datePrecision > 0)) {
            $year = isset($params['year']) ? self::castInt(
                $params['year']
            ) : SelectorInterface::WILDCARD;
        }
        if (($datePrecision > 1)) {
            $month = isset($params['month']) ? self::castInt(
                $params['month']
            ) : SelectorInterface::WILDCARD;
        }
        if (($datePrecision > 2)) {
            $day = isset($params['day']) ? self::castInt($params['day']) : SelectorInterface::WILDCARD;
        }
        if (($datePrecision > 3)) {
            $hour = isset($params['hour']) ? self::castInt(
                $params['hour']
            ) : SelectorInterface::WILDCARD;
        }
        if (($datePrecision > 4)) {
            $minute = isset($params['minute']) ? self::castInt(
                $params['minute']
            ) : SelectorInterface::WILDCARD;
        }
        if (($datePrecision > 5)) {
            $second = isset($params['second']) ? self::castInt(
                $params['second']
            ) : SelectorInterface::WILDCARD;
        }
        $uid = isset($params['id']) ? self::castInt($params['id']) : SelectorInterface::WILDCARD;

        $type = empty($params['type']) ? SelectorInterface::WILDCARD : trim($params['type']);
        $revision = empty($params['revision']) ? Revision::CURRENT : self::castInt($params['revision']);

        return Kernel::create(
            Selector::class,
            [$year, $month, $day, $hour, $minute, $second, $uid, $type, $revision, $visibility, $draft]
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
        return ($value === SelectorInterface::WILDCARD) ? $value : intval($value);
    }
}
