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

use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * Object locator interface
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
interface LocatorInterface
{

    /**
     * Serialize the object locator
     *
     * @return string Object locator
     */
    public function __toString();

    /**
     * Serialize as relative URL
     *
     * @param bool $canonical Canonical URL
     * @return string Relative URL
     */
    public function toUrl($canonical = false);

    /**
     * Return the object's creation date
     *
     * @return \DateTimeInterface Object creation date
     */
    public function getCreationDate();

    /**
     * Set the object's creation date
     *
     * @param \DateTimeInterface $creationDate
     * @return LocatorInterface New object locator
     */
    public function setCreationDate(\DateTimeInterface $creationDate);

    /**
     * Return the object type
     *
     * @return Type Object type
     */
    public function getObjectType();

    /**
     * Set the object type
     *
     * @param Type $type Object type
     * @return LocatorInterface New object locator
     */
    public function setObjectType(Type $type);

    /**
     * Return the object ID
     *
     * @return Id Object ID
     */
    public function getId();

    /**
     * Set the object ID
     *
     * @param Id $uid Object ID
     * @return LocatorInterface New object locator
     */
    public function setId(Id $uid);

    /**
     * Return the object revision
     *
     * @return Revision Object revision
     */
    public function getRevision();

    /**
     * Set the object revision
     *
     * @param Revision $revision Object revision
     * @return LocatorInterface New object locator
     */
    public function setRevision(Revision $revision);

    /**
     * Return the object hidden state
     *
     * @return boolean Object hidden state
     */
    public function isHidden();

    /**
     * Set the object hidden state
     *
     * @param boolean $hidden Object hidden state
     * @return LocatorInterface|Locator New object locator
     */
    public function setHidden($hidden);
}
