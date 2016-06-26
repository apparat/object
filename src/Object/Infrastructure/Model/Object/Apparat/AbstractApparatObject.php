<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Infrastructure
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

namespace Apparat\Object\Infrastructure\Model\Object\Apparat;

use Apparat\Object\Application\Model\Object\ApplicationObjectInterface;
use Apparat\Object\Infrastructure\Model\Object\Apparat\Traits\ApparatObjectTrait;
use Apparat\Object\Ports\Contract\ApparatObjectInterface;

/**
 * Abstract apparat object
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 * @method \DateTimeImmutable getPublished() Return the object publication date
 * @method \DateTimeImmutable getUpdated() Return the object modification date
 * @method array getAuthor() Return the object authors
 * @method array getCategory() Return the object authors
 * @method array getUrl() Return the object URL
 * @method array getUid() Return the object UID
 * @method array getLocation() Return the object location
 * @method array getSyndication() Return the object location
 */
abstract class AbstractApparatObject extends \ArrayObject implements ApparatObjectInterface
{
    /**
     * Use the apparat object common properties
     */
    use ApparatObjectTrait;
    /**
     * Object type
     *
     * @var string
     */
    const TYPE = null;
    /**
     * Application object
     *
     * @var ApplicationObjectInterface
     */
    protected $object;

    /**
     * Apparat object constructor
     *
     * @param ApplicationObjectInterface $object Object
     * @param int $flags Flags
     * @param string $iteratorClass Iterator class
     */
    public function __construct(
        ApplicationObjectInterface $object,
        $flags = 0,
        $iteratorClass = ApparatObjectIterator::class
    ) {
        $this->mapping[ApparatObjectInterface::PROPERTY_TYPE] = ApparatObjectInterface::PROPERTY_TYPE;
        parent::__construct(
            $this->mapping,
            $flags,
            $iteratorClass
        );
        $this->object = $object;
    }

    /**
     * Return the object type
     *
     * @return string Object type
     */
    public function getType()
    {
        return static::TYPE;
    }
}
