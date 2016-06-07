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

use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\AbstractDomainProperties;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Model\Properties\Relations;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Ports\Types\Relation;

/**
 * Apparat article
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 * @method string getName() Return the object name
 * @method string getSummary() Return the object summary
 */
class Article extends AbstractApparatObject
{
    /**
     * Property mapping
     *
     * @var array
     */
    protected $mapping = [
        'name' => MetaProperties::PROPERTY_TITLE,
        'summary' => MetaProperties::PROPERTY_ABSTRACT,
        'content' => ObjectInterface::PROPERTY_PAYLOAD,
        'published' => SystemProperties::PROPERTY_PUBLISHED,
        'updated' => SystemProperties::PROPERTY_MODIFIED,
        'author' => [Relations::COLLECTION, Relation::CONTRIBUTED_BY],
        'category' => MetaProperties::PROPERTY_CATEGORIES,
        'url' => null, // TODO Map to absolute URL
        'uid' => null, // TODO Map to absolute URL
        'location' => SystemProperties::PROPERTY_LOCATION,
        'syndication' => [], // TODO Map to additional relation type?
        'inReplyTo' => [Relations::COLLECTION, Relation::REPLIES_TO],
        'rsvp' => [AbstractDomainProperties::COLLECTION, 'rsvp'],
        'comment' => [AbstractDomainProperties::COLLECTION, 'comment'],
        'likeOf' => [Relations::COLLECTION, Relation::LIKES],
        'repostOf' => [Relations::COLLECTION, Relation::REPOSTS],
        'photo' => [AbstractDomainProperties::COLLECTION, 'photo'],
        'audio' => [AbstractDomainProperties::COLLECTION, 'audio'],
        'repost' => [Relations::COLLECTION, Relation::REPOSTED_BY],
        'featured' => [AbstractDomainProperties::COLLECTION, 'featured'],
    ];
}
