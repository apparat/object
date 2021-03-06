<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Contract;

/**
 * Object types interface
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
interface ObjectTypesInterface
{
    /**
     * Article
     *
     * @var string
     */
    const ARTICLE = 'article';
    /**
     * Audio
     *
     * @var string
     */
    const AUDIO = 'audio';
    /**
     * Bookmark
     *
     * @var string
     */
    const BOOKMARK = 'bookmark';
    /**
     * Checkin
     *
     * @var string
     */
    const CHECKIN = 'checkin';
    /**
     * Cite
     *
     * @var string
     */
    const CITE = 'cite';
    /**
     * Code
     *
     * @var string
     */
    const CODE = 'code';
    /**
     * Contact
     *
     * @var string
     */
    const CONTACT = 'contact';
    /**
     * Address
     *
     * @var string
     */
    const ADDRESS = 'address';
    /**
     * Event
     *
     * @var string
     */
    const EVENT = 'event';
    /**
     * Favourite
     *
     * @var string
     */
    const FAVOURITE = 'favourite';
    /**
     * Geo
     *
     * @var string
     */
    const GEO = 'geo';
    /**
     * Image
     *
     * @var string
     */
    const IMAGE = 'image';
    /**
     * Item
     *
     * @var string
     */
    const ITEM = 'item';
    /**
     * Like
     *
     * @var string
     */
    const LIKE = 'like';
    /**
     * Note
     *
     * @var string
     */
    const NOTE = 'note';
    /**
     * Project
     *
     * @var string
     */
    const PROJECT = 'project';
    /**
     * Reply
     *
     * @var string
     */
    const REPLY = 'reply';
    /**
     * Review
     *
     * @var string
     */
    const REVIEW = 'review';
    /**
     * Rsvp
     *
     * @var string
     */
    const RSVP = 'rsvp';
    /**
     * Venue
     *
     * @var string
     */
    const VENUE = 'venue';
    /**
     * Video
     *
     * @var string
     */
    const VIDEO = 'video';
}
