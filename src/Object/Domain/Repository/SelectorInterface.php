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

/**
 * Repository selector interface
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
interface SelectorInterface
{
    /**
     * Indicator for draft objects
     *
     * @var string
     */
    const INDICATOR_DRAFT = '.';
    /**
     * Indicator for hidden object
     *
     * @var string
     */
    const INDICATOR_HIDDEN = '.';
    /**
     * Indicator for both hidden and visible / draft and regular objects
     *
     * @var string
     */
    const INDICATOR_BOTH = '~';
    /**
     * Wildcard
     *
     * @var string
     */
    const WILDCARD = '*';
    /**
     * Visible objects only
     *
     * @var int
     */
    const VISIBLE = 1;
    /**
     * Hidden objects only
     *
     * @var int
     */
    const HIDDEN = 2;
    /**
     * Published objects only
     *
     * @var int
     */
    const PUBLISHED = 1;
    /**
     * Draft objects only
     *
     * @var int
     */
    const DRAFT = 2;
    /**
     * All objects
     *
     * @var int
     */
    const ALL = 3;

    /**
     * Test if the given argument is a valid object visibility
     *
     * @param int $visibility Object visibility
     * @return boolean Valid visibility
     */
    public static function isValidVisibility($visibility);

    /**
     * Test if the given argument is a valid object draft state
     *
     * @param int $draft Object draft state
     * @return boolean Valid draft state
     */
    public static function isValidDraftState($draft);

    /**
     * Return the year component
     *
     * @return int Year component
     */
    public function getYear();

    /**
     * Return the month component
     *
     * @return int Month component
     */
    public function getMonth();

    /**
     * Return the day component
     *
     * @return int Day component
     */
    public function getDay();

    /**
     * Return the hour component
     *
     * @return int Hour component
     */
    public function getHour();

    /**
     * Return the minute component
     *
     * @return int
     */
    public function getMinute();

    /**
     * Return the second component
     *
     * @return int
     */
    public function getSecond();

    /**
     * Return the ID component
     *
     * @return int ID component
     */
    public function getId();

    /**
     * Return the Type component
     *
     * @return string
     */
    public function getObjectType();

    /**
     * Return the revision component
     *
     * @return int Revision component
     */
    public function getRevision();

    /**
     * Return the object visibility
     *
     * @return int Object visibility
     */
    public function getVisibility();

    /**
     * Return the object draft state
     *
     * @return int Object draft state
     */
    public function getDraft();
}
