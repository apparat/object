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

namespace Apparat\Object\Domain\Repository;

use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * Repository selector
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Selector implements SelectorInterface
{
    /**
     * Year component
     *
     * @var int
     */
    private $year = null;
    /**
     * Month component
     *
     * @var int
     */
    private $month = null;
    /**
     * Day component
     *
     * @var int
     */
    private $day = null;
    /**
     * Hour component
     *
     * @var int
     */
    private $hour = null;
    /**
     * Minute component
     *
     * @var int
     */
    private $minute = null;
    /**
     * Second component
     *
     * @var int
     */
    private $second = null;
    /**
     * Object ID
     *
     * @var int|string
     */
    private $uid = null;
    /**
     * Object type
     *
     * @var string
     */
    private $type = null;
    /**
     * Revision component
     *
     * @var int
     */
    private $revision = null;
    /**
     * Object visibility
     *
     * @var int
     */
    private $visibility = SelectorInterface::VISIBLE;

    /**
     * Repository selector constructor
     *
     * @param string|int|NULL $year
     * @param string|int|NULL $month
     * @param string|int|NULL $day
     * @param string|int|NULL $hour
     * @param string|int|NULL $minute
     * @param string|int|NULL $second
     * @param string|int|NULL $uid Object ID
     * @param string|NULL $type Object type
     * @param int|NULL $revision
     * @param int $visibility
     * @throws InvalidArgumentException If any of the components isn't valid
     */
    public function __construct(
        $year = self::WILDCARD,
        $month = self::WILDCARD,
        $day = self::WILDCARD,
        $hour = self::WILDCARD,
        $minute = self::WILDCARD,
        $second = self::WILDCARD,
        $uid = self::WILDCARD,
        $type = self::WILDCARD,
        $revision = Revision::CURRENT,
        $visibility = SelectorInterface::VISIBLE
    ) {
        $datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));

        // Validate the creation date and ID components
        $dateComponents = [
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'hour' => $hour,
            'minute' => $minute,
            'second' => $second
        ];
        foreach (array_slice($dateComponents, 0, $datePrecision) as $label => $component) {
            // If the component isn't valid
            if (!is_int($component) && ($component !== self::WILDCARD)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Invalid repository selector '.$label.' component "%s"',
                        $component
                    ),
                    InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT,
                    null,
                    $label
                );
            }

            // Set the component value
            $this->$label =
                ($component === self::WILDCARD) ? self::WILDCARD : str_pad($component, 2, '0', STR_PAD_LEFT);
        }

        // If the ID component isn't valid
        if (!is_int($uid) && ($uid !== self::WILDCARD)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid repository selector ID component "%s"',
                    $uid
                ),
                InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT,
                null,
                'id'
            );
        }
        $this->uid = $uid;

        // If the type component isn't valid
        if (!Type::isValidType($type) && ($type !== self::WILDCARD)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid repository selector type component "%s"',
                    $type
                ),
                InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT,
                null,
                'type'
            );
        }
        $this->type = $type;

        // If the revision component isn't valid
        if (!Revision::isValidRevision($revision) && ($revision !== self::WILDCARD)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid repository selector revision component "%s"',
                    $revision
                ),
                InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT,
                null,
                'revision'
            );
        }
        $this->revision = $revision;

        // If the object visibility isn't valid
        if (!self::isValidVisibility($visibility)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid repository selector visibility "%s"',
                    $visibility
                ),
                InvalidArgumentException::INVALID_REPOSITORY_SELECTOR_COMPONENT,
                null,
                'visibility'
            );
        }
        $this->visibility = $visibility;
    }

    /**
     * Return the year component
     *
     * @return int Year component
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Return the month component
     *
     * @return int Month component
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Return the day component
     *
     * @return int Day component
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Return the hour component
     *
     * @return int Hour component
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Return the minute component
     *
     * @return int
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Return the second component
     *
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * Return the ID component
     *
     * @return int ID component
     */
    public function getId()
    {
        return $this->uid;
    }

    /**
     * Return the Type component
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the revision component
     *
     * @return int Revision component
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Return the object visibility
     *
     * @return int Object visibility
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Test if the given argument is a valid object visibility
     *
     * @param int $visibility Object visibility
     * @return boolean Valid visibility
     */
    public static function isValidVisibility($visibility)
    {
        return ($visibility === SelectorInterface::VISIBLE)
        || ($visibility === SelectorInterface::HIDDEN)
        || ($visibility === SelectorInterface::ALL);
    }
}
