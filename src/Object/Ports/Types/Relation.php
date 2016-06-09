<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Ports
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

namespace Apparat\Object\Ports\Types;

use Apparat\Object\Domain\Model\Relation\AbstractRelation;
use Apparat\Object\Domain\Model\Relation\ContributedByRelation;
use Apparat\Object\Domain\Model\Relation\ContributesRelation;
use Apparat\Object\Domain\Model\Relation\EmbeddedByRelation;
use Apparat\Object\Domain\Model\Relation\EmbedsRelation;
use Apparat\Object\Domain\Model\Relation\LikedByRelation;
use Apparat\Object\Domain\Model\Relation\LikesRelation;
use Apparat\Object\Domain\Model\Relation\ReferredByRelation;
use Apparat\Object\Domain\Model\Relation\RefersToRelation;
use Apparat\Object\Domain\Model\Relation\RelationInterface;
use Apparat\Object\Domain\Model\Relation\RepliedByRelation;
use Apparat\Object\Domain\Model\Relation\RepliesToRelation;
use Apparat\Object\Domain\Model\Relation\RepostedByRelation;
use Apparat\Object\Domain\Model\Relation\RepostsRelation;
use Apparat\Object\Domain\Model\Relation\SyndicatedFromRelation;
use Apparat\Object\Domain\Model\Relation\SyndicatedToRelation;

/**
 * Relation types & constants
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Ports
 */
class Relation
{
    /**
     * Contributed-By relation
     *
     * @var string
     */
    const CONTRIBUTED_BY = ContributedByRelation::TYPE;
    /**
     * Contributes relation
     *
     * @var string
     */
    const CONTRIBUTES = ContributesRelation::TYPE;
    /**
     * Embedded-By relation
     *
     * @var string
     */
    const EMBEDDED_BY = EmbeddedByRelation::TYPE;
    /**
     * Embeds relation
     *
     * @var string
     */
    const EMBEDS = EmbedsRelation::TYPE;
    /**
     * Liked-By relation
     *
     * @var string
     */
    const LIKED_BY = LikedByRelation::TYPE;
    /**
     * Likes relation
     *
     * @var string
     */
    const LIKES = LikesRelation::TYPE;
    /**
     * Referred-By relation
     *
     * @var string
     */
    const REFERRED_BY = ReferredByRelation::TYPE;
    /**
     * Refers-To relation
     *
     * @var string
     */
    const REFERS_TO = RefersToRelation::TYPE;
    /**
     * Replied-By relation
     *
     * @var string
     */
    const REPLIED_BY = RepliedByRelation::TYPE;
    /**
     * Replies-To relation
     *
     * @var string
     */
    const REPLIES_TO = RepliesToRelation::TYPE;
    /**
     * Reposted-By relation
     *
     * @var string
     */
    const REPOSTED_BY = RepostedByRelation::TYPE;
    /**
     * Reposts relation
     *
     * @var string
     */
    const REPOSTS = RepostsRelation::TYPE;
    /**
     * Syndicated-To relation
     *
     * @var string
     */
    const SYNDICATED_TO = SyndicatedToRelation::TYPE;
    /**
     * Syndicated-From relation
     *
     * @var string
     */
    const SYNDICATED_FROM = SyndicatedFromRelation::TYPE;
    /**
     * Type property
     *
     * @var string
     */
    const TYPE = RelationInterface::FILTER_TYPE;
    /**
     * URL property
     *
     * @var string
     */
    const URL = RelationInterface::FILTER_URL;
    /**
     * Label property
     *
     * @var string
     */
    const LABEL = RelationInterface::FILTER_LABEL;
    /**
     * Email property
     *
     * @var string
     */
    const EMAIL = RelationInterface::FILTER_EMAIL;
    /**
     * Coupling property
     *
     * @var string
     */
    const COUPLING = RelationInterface::FILTER_COUPLING;
    /**
     * Loose coupling
     *
     * @var int
     */
    const LOOSE_COUPLING = AbstractRelation::LOOSE_COUPLING;
    /**
     * Tight coupling
     *
     * @var int
     */
    const TIGHT_COUPLING = AbstractRelation::TIGHT_COUPLING;
}
