<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\<Layer>
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

namespace Apparat\Object\Domain\Factory;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Model\Path\ApparatUrl;
use Apparat\Object\Domain\Model\Path\Url;
use Apparat\Object\Domain\Model\Relation\ContributedByRelation;
use Apparat\Object\Domain\Model\Relation\ContributesRelation;
use Apparat\Object\Domain\Model\Relation\EmbeddedByRelation;
use Apparat\Object\Domain\Model\Relation\EmbedsRelation;
use Apparat\Object\Domain\Model\Relation\InvalidArgumentException;
use Apparat\Object\Domain\Model\Relation\LikedByRelation;
use Apparat\Object\Domain\Model\Relation\LikesRelation;
use Apparat\Object\Domain\Model\Relation\OutOfBoundsException;
use Apparat\Object\Domain\Model\Relation\ReferredByRelation;
use Apparat\Object\Domain\Model\Relation\RefersToRelation;
use Apparat\Object\Domain\Model\Relation\RelationInterface;
use Apparat\Object\Domain\Model\Relation\RepliedByRelation;
use Apparat\Object\Domain\Model\Relation\RepliesToRelation;
use Apparat\Object\Domain\Model\Relation\RepostedByRelation;
use Apparat\Object\Domain\Model\Relation\RepostsRelation;
use Apparat\Object\Domain\Model\Relation\SyndicatedFromRelation;
use Apparat\Object\Domain\Model\Relation\SyndicatedToRelation;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Infrastructure\Utilities\Validator;

/**
 * Relation factory
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class RelationFactory
{
    /**
     * URL component key
     *
     * @string
     */
    const PARSE_URL = 'url';
    /**
     * Label component key
     *
     * @string
     */
    const PARSE_LABEL = 'label';
    /**
     * Email component key
     *
     * @string
     */
    const PARSE_EMAIL = 'email';
    /**
     * Component relation coupling
     *
     * @string
     */
    const PARSE_COUPLING = 'coupling';
    /**
     * Relation types
     *
     * @var array
     */
    public static $relationTypes = [
        ContributesRelation::TYPE => ContributesRelation::class,
        ContributedByRelation::TYPE => ContributedByRelation::class,
        EmbedsRelation::TYPE => EmbedsRelation::class,
        EmbeddedByRelation::TYPE => EmbeddedByRelation::class,
        LikesRelation::TYPE => LikesRelation::class,
        LikedByRelation::TYPE => LikedByRelation::class,
        RefersToRelation::TYPE => RefersToRelation::class,
        ReferredByRelation::TYPE => ReferredByRelation::class,
        RepliesToRelation::TYPE => RepliesToRelation::class,
        RepliedByRelation::TYPE => RepliedByRelation::class,
        RepostsRelation::TYPE => RepostsRelation::class,
        RepostedByRelation::TYPE => RepostedByRelation::class,
        SyndicatedToRelation::TYPE => SyndicatedToRelation::class,
        SyndicatedFromRelation::TYPE => SyndicatedFromRelation::class,
    ];

    /**
     * Parse a relation serialization and instantiate the relation
     *
     * @param string $relationType Relation type
     * @param string $relation Relation serialization
     * @param RepositoryInterface $contextRepository Context repository
     * @return RelationInterface Relation object
     */
    public static function createFromString($relationType, $relation, RepositoryInterface $contextRepository)
    {
        // Validate the relation type
        self::validateRelationType($relationType);

        // Create the relation instance
        return Kernel::create(
            self::$relationTypes[$relationType],
            array_values(self::parseRelationString($relation, $contextRepository))
        );
    }

    /**
     * Validate a relation type
     *
     * @param string $relationType Relation type
     * @throws InvalidArgumentException If the relation type is invalid
     */
    public static function validateRelationType($relationType)
    {
        // If the relation type is invalid
        if (empty($relationType) || empty(self::$relationTypes[$relationType])) {
            throw new OutOfBoundsException(
                sprintf('Invalid object relation type "%s"', $relationType),
                OutOfBoundsException::INVALID_OBJECT_RELATION_TYPE
            );
        }
    }

    /**
     * Parse a relation serialization and instantiate the relation object
     *
     * @param string $relation Relation serialization
     * @param RepositoryInterface $contextRepository Context repository
     * @return array Parsed relation components
     * @throws InvalidArgumentException If the email component has already been registered
     * @throws InvalidArgumentException If the URL component has already been registered
     */
    protected static function parseRelationString($relation, RepositoryInterface $contextRepository)
    {
        $parsed = [
            self::PARSE_URL => null,
            self::PARSE_LABEL => null,
            self::PARSE_EMAIL => null,
            self::PARSE_COUPLING => RelationInterface::LOOSE_COUPLING,
        ];

        // Split the relation string and parse the components
        foreach (preg_split('%\s+%', $relation) as $relationComponent) {
            // If it's an email component
            if (!strncmp('<', $relationComponent, 1)) {
                // If the email component has already been registered
                if (!empty($parsed[self::PARSE_EMAIL])) {
                    throw new InvalidArgumentException(
                        sprintf('Repeated relation email component "%s" not allowed', self::PARSE_EMAIL),
                        InvalidArgumentException::REPEATED_RELATION_COMPONENT_NOT_ALLOWED
                    );
                }

                $parsed[self::PARSE_EMAIL] = self::parseRelationEmail($relationComponent);
                continue;
            }

            // Next: Try to parse it as URL
            try {
                $parsed[self::PARSE_COUPLING] = intval($parsed[self::PARSE_COUPLING]);
                $url = self::parseRelationUrl(
                    $relationComponent,
                    $parsed[self::PARSE_COUPLING],
                    $contextRepository
                );

                // If the URL component has already been registered
                if (!empty($parsed[self::PARSE_URL])) {
                    throw new InvalidArgumentException(
                        sprintf('Repeated relation url component "%s" not allowed', self::PARSE_URL),
                        InvalidArgumentException::REPEATED_RELATION_COMPONENT_NOT_ALLOWED
                    );
                }

                $parsed[self::PARSE_URL] = $url;

                // Else: Process as label component
            } catch (\Exception $e) {
                // If it's a repeated URL component
                if (($e instanceof InvalidArgumentException)
                    && ($e->getCode() == InvalidArgumentException::REPEATED_RELATION_COMPONENT_NOT_ALLOWED)
                ) {
                    throw $e;
                }

                $parsed[self::PARSE_LABEL] = trim($parsed[self::PARSE_LABEL].' '.$relationComponent);
            }
        }

        return $parsed;
    }

    /**
     * Parse and validate a relation email address component
     *
     * @param string $relationEmail Email address
     * @return string Valid email address
     * @throws InvalidArgumentException If the email address is invalid
     */
    protected static function parseRelationEmail($relationEmail)
    {
        // If it's a valid email address
        if (preg_match('%^\<(.+)\>$%', $relationEmail, $emailAddress) && Validator::isEmail($emailAddress[1])) {
            return $emailAddress[1];
        }

        throw new InvalidArgumentException(
            sprintf('Invalid relation email address "%s"', $relationEmail),
            InvalidArgumentException::INVALID_RELATION_EMAIL_ADDRESS
        );
    }

    /**
     * Parse and instantiate a relation URL
     *
     * @param string $url URL
     * @param int $coupling Strong coupling
     * @param RepositoryInterface $contextRepository Context repository
     * @return Url URL
     * @throws InvalidArgumentException If the relation URL is invalid
     */
    protected static function parseRelationUrl($url, &$coupling, RepositoryInterface $contextRepository)
    {
        if (strlen($url)) {
            // If the URL requires tight coupling
            if (!strncmp('!', $url, 1)) {
                $coupling = RelationInterface::TIGHT_COUPLING;
                $url = substr($url, 1);
            }

            // Try to instantiate as an apparat URL
            try {
                return Kernel::create(ApparatUrl::class, [$url, true, $contextRepository]);

                // If there's an apparat URL problem: Try to instantiate as a regular URL
            } catch (\Apparat\Object\Domain\Model\Path\InvalidArgumentException $e) {
                /** @var Url $urlInstance */
                $urlInstance = Kernel::create(Url::class, [$url]);
                if ($urlInstance->isAbsolute()) {
                    return $urlInstance;
                }
            }
        }

        throw new InvalidArgumentException(
            sprintf('Invalid relation URL "%s"', $url),
            InvalidArgumentException::INVALID_RELATION_URL
        );
    }
}
