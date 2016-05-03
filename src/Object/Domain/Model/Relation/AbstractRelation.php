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

namespace Apparat\Object\Domain\Model\Relation;

/**
 * Abstract base relation
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
abstract class AbstractRelation implements RelationInterface
{
    /**
     * Relation types
     *
     * @var array
     */
    protected static $types = [
        self::CONTRIBUTES => true,
        self::CONTRIBUTED_BY => true,
        self::EMBEDS => true,
        self::EMBEDDED_BY => true,
        self::LIKES => true,
        self::LIKED_BY => true,
        self::REFERS_TO => true,
        self::REFERRED_BY => true,
        self::REPLIES_TO => true,
        self::REPLIED_BY => true,
        self::REPOSTS => true,
        self::REPOSTED_BY => true,
    ];
    /**
     * Relation type
     *
     * @var string
     */
    protected $type = null;
    /**
     * Coupling
     *
     * @var int
     */
    protected $coupling = self::LOOSE_COUPLING;

    /**
     * Constructor
     *
     * @param string $type Relation type
     * @param int $coupling Coupling
     * @throws OutOfBoundsException If the object type is invalid
     * @throws OutOfBoundsException If the object coupling is invalid
     */
    public function construct($type, $coupling = self::LOOSE_COUPLING)
    {
        // If the object type is invalid
        if (!strlen($type) || !array_key_exists($type, self::$types)) {
            throw new OutOfBoundsException(
                sprintf('Invalid object relation type "%s"', $type),
                OutOfBoundsException::INVALID_OBJECT_RELATION_TYPE
            );
        }

        // If the coupling type is invalid
        if (($coupling !== self::LOOSE_COUPLING) && ($coupling !== self::TIGHT_COUPLING)) {
            throw new OutOfBoundsException(
                sprintf('Invalid object coupling "%s"', $type),
                OutOfBoundsException::INVALID_OBJECT_COUPLING
            );
        }

        $this->type = $type;
        $this->coupling = $coupling;
    }
}
