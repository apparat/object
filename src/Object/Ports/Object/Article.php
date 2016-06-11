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

use Apparat\Object\Application\Model\Properties\Domain\Article as ArticleProperties;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\AbstractProperties;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Model\Properties\Relations;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Infrastructure\Model\Object\Apparat\AbstractApparatObject;
use Apparat\Object\Ports\Types\Relation;

/**
 * Apparat article
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 * @method string getName() Return the object name
 * @method string getSummary() Return the object summary
 * @method string getContent() Return the object content
 */
class Article extends AbstractApparatObject
{
    /**
     * Property mapping
     *
     * @var array
     */
    protected $mapping = [
        ArticleProperties::PUBLISHED => SystemProperties::PROPERTY_PUBLISHED,
        ArticleProperties::UPDATED => SystemProperties::PROPERTY_MODIFIED,
        ArticleProperties::AUTHOR => [Relations::COLLECTION, Relation::CONTRIBUTED_BY],
        ArticleProperties::CATEGORY => MetaProperties::PROPERTY_CATEGORIES,
        ArticleProperties::URL => AbstractProperties::PROPERTY_ABSOLUTE_URL,
        ArticleProperties::UID => AbstractProperties::PROPERTY_CANONICAL_URL,
        ArticleProperties::LOCATION => SystemProperties::PROPERTY_LOCATION,
        ArticleProperties::SYNDICATION => [Relations::COLLECTION, Relation::SYNDICATED_TO],

        ArticleProperties::NAME => MetaProperties::PROPERTY_TITLE,
        ArticleProperties::SUMMARY => MetaProperties::PROPERTY_ABSTRACT,
        ArticleProperties::CONTENT => ObjectInterface::PROPERTY_PAYLOAD,
        ArticleProperties::FEATURED => [ArticleProperties::COLLECTION, ArticleProperties::FEATURED],

//        'inReplyTo' => [Relations::COLLECTION, Relation::REPLIES_TO],
//        'rsvp' => [AbstractDomainProperties::COLLECTION, 'rsvp'],
//        'likeOf' => [Relations::COLLECTION, Relation::LIKES],
//        'repostOf' => [Relations::COLLECTION, Relation::REPOSTS],
//        'photo' => [AbstractDomainProperties::COLLECTION, 'photo'],
//        'audio' => [AbstractDomainProperties::COLLECTION, 'audio'],
//        'repost' => [Relations::COLLECTION, Relation::REPOSTED_BY],
//        'featured' => [AbstractDomainProperties::COLLECTION, 'featured'],
    ];
}
