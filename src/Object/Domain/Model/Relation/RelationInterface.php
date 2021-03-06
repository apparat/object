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

use Apparat\Object\Domain\Model\Uri\ApparatUrl;
use Apparat\Object\Domain\Model\Uri\Url;

/**
 * Relation interface
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
interface RelationInterface
{
    /**
     * Loose coupling
     *
     * @var int
     */
    const LOOSE_COUPLING = 0;
    /**
     * Tight coupling (⚭)
     *
     * @var int
     */
    const TIGHT_COUPLING = 1;
    /**
     * Type property
     *
     * @var string
     */
    const FILTER_TYPE = 'type';
    /**
     * URL property
     *
     * @var string
     */
    const FILTER_URL = 'url';
    /**
     * Label property
     *
     * @var string
     */
    const FILTER_LABEL = 'label';
    /**
     * Email property
     *
     * @var string
     */
    const FILTER_EMAIL = 'email';
    /**
     * Coupling property
     *
     * @var string
     */
    const FILTER_COUPLING = 'coupling';

    /**
     * Return the relation type
     *
     * @return string Relation type
     */
    public function getRelationType();

    /**
     * Return the URL
     *
     * @return Url|ApparatUrl URL
     */
    public function getUrl();

    /**
     * Set the URL
     *
     * @param Url|ApparatUrl|null $url URL
     * @return RelationInterface Self reference
     */
    public function setUrl(Url $url = null);

    /**
     * Return the label
     *
     * @return string|null Label
     */
    public function getLabel();

    /**
     * Set the label
     *
     * @param string|null $label Label
     * @return RelationInterface Self reference
     */
    public function setLabel($label);

    /**
     * Return the email address
     *
     * @return string|null Email address
     */
    public function getEmail();

    /**
     * Set the email address
     *
     * @param string|null $email Email address
     * @return RelationInterface Self reference
     */
    public function setEmail($email);

    /**
     * Return the coupling
     *
     * @return int Coupling
     */
    public function getCoupling();

    /**
     * Set the coupling
     *
     * @param int $coupling Coupling
     * @return RelationInterface Self reference
     * @throws OutOfBoundsException If the object coupling is invalid
     */
    public function setCoupling($coupling);

    /**
     * Return the unique relation signature
     *
     * @return string Relation signature
     */
    public function getSignature();
}
