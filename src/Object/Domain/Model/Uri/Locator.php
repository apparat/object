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
 * Object path
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
     * @param null|string $path Object path
     * @param null|boolean|int $datePrecision Date precision [NULL = local default, TRUE = any precision (remote object
     *     URLs)]
     * @param string $leader Leading base path
     * @throws InvalidArgumentException If the object URL path is invalid
     */
    public function __construct($path = null, $datePrecision = null, &$leader = '')
    {
        if (!empty($path)) {
            // If the local default date precision should be used
            if ($datePrecision === null) {
                $datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));
            }

            // Build the regular expression for matching a local path
            $pathPattern = $this->buildPathRegex($datePrecision);

            // Match the local path
            if (!preg_match($pathPattern, $path, $pathParts)) {
                throw new InvalidArgumentException(
                    sprintf('Invalid object URL path "%s"', $path),
                    InvalidArgumentException::INVALID_OBJECT_URL_LOCATOR
                );
            }

            // If date components are used
            if ($datePrecision) {
                $year = $pathParts['year'];
                $month = isset($pathParts['month']) ? $pathParts['month'] ?: '01' : '01';
                $day = isset($pathParts['day']) ? $pathParts['day'] ?: '01' : '01';
                $hour = isset($pathParts['hour']) ? $pathParts['hour'] ?: '00' : '00';
                $minute = isset($pathParts['minute']) ? $pathParts['minute'] ?: '00' : '00';
                $second = isset($pathParts['second']) ? $pathParts['second'] ?: '00' : '00';
                $this->creationDate = new \DateTimeImmutable("$year-$month-$day".'T'."$hour:$minute:$second+00:00");
            }

            // Determine the leader
            $leader = ($datePrecision === true) ? substr(
                $path,
                0,
                strlen($path) - strlen($pathParts[0])
            ) : $pathParts['leader'];

            // Set the hidden state
            $this->hidden = !empty($pathParts['hidden']);

            // Set the ID
            $this->uid = Kernel::create(Id::class, [intval($pathParts['id'])]);

            // Set the type
            $this->type = Kernel::create(Type::class, [$pathParts['type']]);

            // Set the revision
            $this->revision = Kernel::create(
                Revision::class,
                [
                    empty($pathParts['revision']) ? Revision::CURRENT : intval($pathParts['revision']),
                    !empty($pathParts['draft'])
                ]
            );
        }
    }

    /**
     * Create and return the object URL path
     *
     * @return string Object path
     */
    public function __toString()
    {
        $path = [];
        $datePrecision = intval(getenv('OBJECT_DATE_PRECISION'));

        // Add the creation date
        foreach (array_slice(array_keys(self::$datePattern), 0, $datePrecision) as $dateFormat) {
            $path[] = $this->creationDate->format($dateFormat);
        }

        // Add the object ID and type
        $path[] = ($this->hidden ? '.' : '').$this->uid->getId().'-'.$this->type->getType();

        // Add the ID, draft mode and revision
        $uid = $this->uid->getId();
        $path[] = rtrim(($this->revision->isDraft() ? '.' : '').$uid.'-'.$this->revision->getRevision(), '-');

        return '/'.implode('/', $path);
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
     * @return LocatorInterface|Locator New object path
     */
    public function setCreationDate(\DateTimeInterface $creationDate)
    {
        $path = clone $this;
        $path->creationDate = $creationDate;
        return $path;
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
     * @return LocatorInterface|Locator New object path
     */
    public function setType(Type $type)
    {
        $path = clone $this;
        $path->type = $type;
        return $path;
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
     * @return LocatorInterface|Locator New object path
     */
    public function setId(Id $uid)
    {
        $path = clone $this;
        $path->uid = $uid;
        return $path;
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
     * @return LocatorInterface|Locator New object path
     */
    public function setRevision(Revision $revision)
    {
        $path = clone $this;
        $path->revision = $revision;
        return $path;
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
     * @return LocatorInterface|Locator New object path
     */
    public function setHidden($hidden)
    {
        $path = clone $this;
        $path->hidden = !!$hidden;
        return $path;
    }

    /**
     * Build the regular expression for matching a local object path
     *
     * @param null|boolean|int $datePrecision Date precision [NULL = local default, TRUE = any precision (remote object
     *     URLs)]
     * @return string Regular expression for matching a local object path
     * @throws InvalidArgumentException If the date precision is invalid
     */
    protected function buildPathRegex($datePrecision)
    {
        $pathPattern = null;

        // If a valid integer date precision is given
        if (is_int($datePrecision) && ($datePrecision >= 0) && ($datePrecision < 7)) {
            $pathPattern = '%^(?P<leader>(/[^/]+)*)?/'.
                implode(
                    '/',
                    array_slice(self::$datePattern, 0, $datePrecision)
                ).($datePrecision ? '/' : '');

            // Else if the date precision may be arbitrary
        } elseif ($datePrecision === true) {
            $pathPattern = '%(?:/'.implode('(?:/', self::$datePattern);
            $pathPattern .= str_repeat(')?', count(self::$datePattern));
            $pathPattern .= '/';
        }

        // If the date precision is invalid
        if ($pathPattern === null) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid date precision "%s" (%s)',
                    strval($datePrecision),
                    gettype($datePrecision)
                ),
                InvalidArgumentException::INVALID_DATE_PRECISION
            );
        }

        $pathPattern .= '(?P<hidden>\.)?(?P<id>\d+)\-(?P<type>[a-z]+)(?:/(?P<draft>\.)?(.*\.)?';
        $pathPattern .= '\\k<id>(?:-(?P<revision>\d+))?(?P<extension>\.[a-z0-9]+)?)?$%';

        return $pathPattern;
    }
}