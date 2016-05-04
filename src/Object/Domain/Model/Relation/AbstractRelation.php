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

use Apparat\Object\Domain\Model\Path\Url;

/**
 * Abstract base relation
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
abstract class AbstractRelation implements RelationInterface
{
    /**
     * Relation type
     *
     * @var string
     */
    const TYPE = 'abstract';
    /**
     * Relation URL
     *
     * @var Url
     */
    protected $url = null;
    /**
     * Relation label
     *
     * @var string
     */
    protected $label = null;
    /**
     * Relation email
     *
     * @var string
     */
    protected $email = null;
    /**
     * Coupling
     *
     * @var int
     */
    protected $coupling = self::LOOSE_COUPLING;

    /**
     * @param Url $url Relation URL
     * @param string $label Relation label
     * @param string $email Relation email
     * @param int $coupling Coupling
     * @throws OutOfBoundsException If the object coupling is invalid
     */
    public function __construct(
        Url $url = null,
        $label = null,
        $email = null,
        $coupling = null
    ) {
        // If the coupling type is invalid
        if (($coupling !== self::LOOSE_COUPLING) && ($coupling !== self::TIGHT_COUPLING)) {
            throw new OutOfBoundsException(
                sprintf('Invalid object coupling "%s"', $coupling),
                OutOfBoundsException::INVALID_OBJECT_COUPLING
            );
        }

        $this->url = $url;
        $this->label = $label;
        $this->email = $email;
        $this->coupling = $coupling;
    }

    /**
     * Return the relation type
     *
     * @return string Relation type
     */
    public function getType()
    {
        return static::TYPE;
    }

    /**
     * Return the URL
     *
     * @return Url URL
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the URL
     *
     * @param Url|null $url URL
     * @return RelationInterface Self reference
     */
    public function setUrl(Url $url = null)
    {
        $relation = clone $this;
        $relation->url = $url;
        return $relation;
    }

    /**
     * Return the label
     *
     * @return string|null Label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label
     *
     * @param string|null $label Label
     * @return RelationInterface Self reference
     */
    public function setLabel($label)
    {
        $relation = clone $this;
        $relation->label = $label;
        return $relation;
    }

    /**
     * Return the email address
     *
     * @return string|null Email address
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the email address
     *
     * @param string|null $email Email address
     * @return RelationInterface Self reference
     */
    public function setEmail($email)
    {
        $relation = clone $this;
        $relation->email = $email;
        return $relation;
    }

    /**
     * Return the coupling
     *
     * @return int Coupling
     */
    public function getCoupling()
    {
        return $this->coupling;
    }

    /**
     * Set the coupling
     *
     * @param int $coupling Coupling
     * @return RelationInterface Self reference
     * @throws OutOfBoundsException If the object coupling is invalid
     */
    public function setCoupling($coupling)
    {
        // If the coupling type is invalid
        if (($coupling !== self::LOOSE_COUPLING) && ($coupling !== self::TIGHT_COUPLING)) {
            throw new OutOfBoundsException(
                sprintf('Invalid object coupling "%s"', $coupling),
                OutOfBoundsException::INVALID_OBJECT_COUPLING
            );
        }

        $relation = clone $this;
        $relation->coupling = $coupling;
        return $relation;
    }

    /**
     * Return the unique relation signature
     *
     * @return string Relation signature
     */
    public function getSignature()
    {
        return md5(
            empty($this->url) ?
                (empty($this->email) ?
                    (empty($this->label) ?
                        '-' : $this->label)
                    : $this->email)
                : $this->url);
    }
}
