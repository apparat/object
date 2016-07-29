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

namespace Apparat\Object\Ports\Object;

use Apparat\Object\Application\Model\Properties\Domain\Note as NoteProperties;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\AbstractProperties;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Model\Properties\Relations;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Infrastructure\Model\Object\Apparat\AbstractApparatObject;
use Apparat\Object\Ports\Types\Object;
use Apparat\Object\Ports\Types\Relation;

/**
 * Apparat note
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Ports
 * @method string getName() Return the object name
 * @method string getSummary() Return the object summary
 * @method string getContent() Return the object content
 */
class Note extends AbstractApparatObject
{
    /**
     * Object type
     *
     * @var string
     */
    const TYPE = Object::NOTE;
    /**
     * Property mapping
     *
     * @var array
     */
    protected $mapping = [
        NoteProperties::PUBLISHED => SystemProperties::PROPERTY_PUBLISHED,
        NoteProperties::UPDATED => SystemProperties::PROPERTY_MODIFIED,
        NoteProperties::AUTHOR => [Relations::COLLECTION, Relation::CONTRIBUTED_BY],
        NoteProperties::CATEGORY => MetaProperties::PROPERTY_CATEGORIES,
        NoteProperties::URL => AbstractProperties::PROPERTY_ABSOLUTE_URL,
        NoteProperties::UID => AbstractProperties::PROPERTY_CANONICAL_URL,
        NoteProperties::LOCATION => [NoteProperties::COLLECTION, NoteProperties::LOCATION],
        NoteProperties::SYNDICATION => [Relations::COLLECTION, Relation::SYNDICATED_TO],

        NoteProperties::NAME => MetaProperties::PROPERTY_TITLE,
        NoteProperties::SUMMARY => MetaProperties::PROPERTY_ABSTRACT,
        NoteProperties::CONTENT => ObjectInterface::PROPERTY_PAYLOAD,
    ];
}
