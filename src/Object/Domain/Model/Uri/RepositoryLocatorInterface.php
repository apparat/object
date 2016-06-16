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
use Apparat\Object\Domain\Repository\RepositoryInterface;

/**
 * Repository path interface
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
interface RepositoryLocatorInterface extends LocatorInterface
{
    /**
     * Return the repository this path applies to
     *
     * @return RepositoryInterface Repository
     */
    public function getRepository();

    /**
     * Set the object's creation date
     *
     * @param \DateTimeInterface $creationDate
     * @return RepositoryLocatorInterface New object path
     */
    public function setCreationDate(\DateTimeInterface $creationDate);

    /**
     * Set the object type
     *
     * @param Type $type Object type
     * @return RepositoryLocatorInterface New object path
     */
    public function setType(Type $type);

    /**
     * Set the object ID
     *
     * @param Id $uid Object ID
     * @return RepositoryLocatorInterface New object path
     */
    public function setId(Id $uid);

    /**
     * Set the object revision
     *
     * @param Revision $revision Object revision
     * @return RepositoryLocatorInterface New object path
     */
    public function setRevision(Revision $revision);

    /**
     * Return the repository relative object path with a file extension
     *
     * @param string $extension File extension
     * @return string Repository relative object path with extension
     */
    public function withExtension($extension);

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
     * @return LocatorInterface|Locator New object path
     */
    public function setHidden($hidden);
}
