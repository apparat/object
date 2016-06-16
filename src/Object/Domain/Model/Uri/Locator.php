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

namespace Apparat\Object\Domain\Model\Uri;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * Object locator
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Locator implements LocatorInterface
{
    /**
     * Date PCRE pattern
     *
     * @var array
     */
    protected static $datePattern = [
        'Y' => '(?P<year>\d{4})',
        'm' => '(?P<month>\d{2})',
        'd' => '(?P<day>\d{2})',
        'H' => '(?P<hour>\d{2})',
        'i' => '(?P<minute>\d{2})',
        's' => '(?P<second>\d{2})',
    ];
    /**
     * Creation date
     *
     * @var \DateTimeInterface
     */
    protected $creationDate = null;
    /**
     * Object ID
     *
     * @var Id
     */
    protected $uid = null;
    /**
     * Object type
     *
     * @var Type
     */
    protected $type = null;
    /**
     * Object revision
     *
     * @var Revision
     */
    protected $revision = null;
    /**
     * Hidden object
     *
     * @var boolean
     */
    protected $hidden = false;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Object URL constructor
     *
     * @param null|string $locator Object locator
     * @param null|boolean|int $datePrecision Date precision [NULL = local default, TRUE = any precision (remote object
     *     URLs)]
     * @param string $leader Leading base locator
     * @throws InvalidArgumentException If the object URL locator is invalid
     */
    public function __construct($locator = null, $datePrecision = null, &$leader = '')
    {
        if (!empty($locator)) {
            // If the local default date precision should be used
            if ($datePrecision === null) {
                $datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));
            }

            // Build the regular expression for matching a local locator
            $locatorPattern = $this->buildLocatorRegex($datePrecision);

            // Match the local locator
            if (!preg_match($locatorPattern, $locator, $locatorParts)) {
                throw new InvalidArgumentException(
                    sprintf('Invalid object URL locator "%s"', $locator),
                    InvalidArgumentException::INVALID_OBJECT_URL_LOCATOR
                );
            }

            // If date components are used
            if ($datePrecision) {
                $year = $locatorParts['year'];
                $month = isset($locatorParts['month']) ? $locatorParts['month'] ?: '01' : '01';
                $day = isset($locatorParts['day']) ? $locatorParts['day'] ?: '01' : '01';
                $hour = isset($locatorParts['hour']) ? $locatorParts['hour'] ?: '00' : '00';
                $minute = isset($locatorParts['minute']) ? $locatorParts['minute'] ?: '00' : '00';
                $second = isset($locatorParts['second']) ? $locatorParts['second'] ?: '00' : '00';
                $this->creationDate = new \DateTimeImmutable("$year-$month-$day".'T'."$hour:$minute:$second+00:00");
            }

            // Determine the leader
            $leader = ($datePrecision === true) ? substr(
                $locator,
                0,
                strlen($locator) - strlen($locatorParts[0])
            ) : $locatorParts['leader'];

            // Set the hidden state
            $this->hidden = !empty($locatorParts['hidden']);

            // Set the ID
            $this->uid = Kernel::create(Id::class, [intval($locatorParts['id'])]);

            // Set the type
            $this->type = Kernel::create(Type::class, [$locatorParts['type']]);

            // Set the revision
            $this->revision = Kernel::create(
                Revision::class,
                [
                    empty($locatorParts['revision']) ? Revision::CURRENT : intval($locatorParts['revision']),
                    !empty($locatorParts['draft'])
                ]
            );
        }
    }

    /**
     * Create and return the object URL locator
     *
     * @return string Object locator
     */
    public function __toString()
    {
        $locator = [];
        $datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));

        // Add the creation date
        foreach (array_slice(array_keys(self::$datePattern), 0, $datePrecision) as $dateFormat) {
            $locator[] = $this->creationDate->format($dateFormat);
        }

        // Add the object ID and type
        $locator[] = ($this->hidden ? '.' : '').$this->uid->getId().'-'.$this->type->getType();

        // Add the ID, draft mode and revision
        $uid = $this->uid->getId();
        $locator[] = rtrim(($this->revision->isDraft() ? '.' : '').$uid.'-'.$this->revision->getRevision(), '-');

        return '/'.implode('/', $locator);
    }

    /**
     * Return the object's creation date
     *
     * @return \DateTimeInterface Object creation date
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set the object's creation date
     *
     * @param \DateTimeInterface $creationDate
     * @return LocatorInterface|Locator New object locator
     */
    public function setCreationDate(\DateTimeInterface $creationDate)
    {
        $locator = clone $this;
        $locator->creationDate = $creationDate;
        return $locator;
    }

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the object type
     *
     * @param Type $type Object type
     * @return LocatorInterface|Locator New object locator
     */
    public function setType(Type $type)
    {
        $locator = clone $this;
        $locator->type = $type;
        return $locator;
    }

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId()
    {
        return $this->uid;
    }

    /**
     * Set the object ID
     *
     * @param Id $uid Object ID
     * @return LocatorInterface|Locator New object locator
     */
    public function setId(Id $uid)
    {
        $locator = clone $this;
        $locator->uid = $uid;
        return $locator;
    }

    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Set the object revision
     *
     * @param Revision $revision Object revision
     * @return LocatorInterface|Locator New object locator
     */
    public function setRevision(Revision $revision)
    {
        $locator = clone $this;
        $locator->revision = $revision;
        return $locator;
    }

    /**
     * Return the object hidden state
     *
     * @return boolean Object hidden state
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * Set the object hidden state
     *
     * @param boolean $hidden Object hidden state
     * @return LocatorInterface|Locator New object locator
     */
    public function setHidden($hidden)
    {
        $locator = clone $this;
        $locator->hidden = !!$hidden;
        return $locator;
    }

    /**
     * Build the regular expression for matching a local object locator
     *
     * @param null|boolean|int $datePrecision Date precision [NULL = local default, TRUE = any precision (remote object
     *     URLs)]
     * @return string Regular expression for matching a local object locator
     * @throws InvalidArgumentException If the date precision is invalid
     */
    protected function buildLocatorRegex($datePrecision)
    {
        $locatorPattern = null;

        // If a valid integer date precision is given
        if (is_int($datePrecision) && ($datePrecision >= 0) && ($datePrecision < 7)) {
            $locatorPattern = '%^(?P<leader>(/[^/]+)*)?/'.
                implode(
                    '/',
                    array_slice(self::$datePattern, 0, $datePrecision)
                ).($datePrecision ? '/' : '');

            // Else if the date precision may be arbitrary
        } elseif ($datePrecision === true) {
            $locatorPattern = '%(?:/'.implode('(?:/', self::$datePattern);
            $locatorPattern .= str_repeat(')?', count(self::$datePattern));
            $locatorPattern .= '/';
        }

        // If the date precision is invalid
        if ($locatorPattern === null) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid date precision "%s" (%s)',
                    strval($datePrecision),
                    gettype($datePrecision)
                ),
                InvalidArgumentException::INVALID_DATE_PRECISION
            );
        }

        $locatorPattern .= '(?P<hidden>\.)?(?P<id>\d+)\-(?P<type>[a-z]+)(?:/(?P<draft>\.)?(.*\.)?';
        $locatorPattern .= '\\k<id>(?:-(?P<revision>\d+))?(?P<extension>\.[a-z0-9]+)?)?$%';

        return $locatorPattern;
    }
}
