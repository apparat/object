<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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

namespace Apparat\Object\Domain\Model\Properties;

use Apparat\Object\Domain\Model\Object\ObjectInterface;

/**
 * Object resource relations
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class Relations extends AbstractProperties
{
    /**
     * Collection name
     *
     * @var string
     */
    const COLLECTION = 'relations';
    /**
     * Active resource referral
     *
     * @var string
     */
    const REFERS_TO = 'refers-to';
    /**
     * Passive resource referral
     *
     * @var string
     */
    const REFERRED_BY = 'referred-by';
    /**
     * Active resource embedding
     *
     * @var string
     */
    const EMBEDS = 'embeds';
    /**
     * Passive resource embedding
     *
     * @var string
     */
    const EMBEDDED_BY = 'embedded-by';
    /**
     * Active resource reply
     *
     * @var string
     */
    const REPLIES_TO = 'replies-to';
    /**
     * Passive resource reply
     *
     * @var string
     */
    const REPLIED_BY = 'replied-by';
    /**
     * Active resource liking
     *
     * @var string
     */
    const LIKES = 'likes';
    /**
     * Passive resource liking
     *
     * @var string
     */
    const LIKED_BY = 'liked-by';
    /**
     * Active resource re-posting
     *
     * @var string
     */
    const REPOSTS = 'reposts';
    /**
     * Passive resource re-posting
     *
     * @var string
     */
    const REPOSTED_BY = 'reposted-by';

    /**
     * Relations constructor
     *
     * @param array $data Property data
     * @param ObjectInterface $object Owner object
     */
    public function __construct(array $data, ObjectInterface $object)
    {
        parent::__construct($data, $object);
    }
}
