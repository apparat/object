<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application
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

namespace Apparat\Object\Application\Model\Properties\Domain;

use Apparat\Object\Application\Model\Properties\Domain\Traits\ContactPropertiesModelTrait;

/**
 * Contact object domain properties
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 */
class Contact extends AbstractDomainProperties
{
    /**
     * Use the contact properties model
     */
    use ContactPropertiesModelTrait;
    /**
     * Given name
     *
     * @var string
     */
    const GIVEN_NAME = 'givenName';
    /**
     * Given name
     *
     * @var string
     */
    const ADDITIONAL_NAME = 'additionalName';
    /**
     * Nickname
     *
     * @var string
     */
    const NICKNAME = 'nickname';
    /**
     * Family name
     *
     * @var string
     */
    const FAMILY_NAME = 'familyName';
    /**
     * Honorific prefix
     *
     * @var string
     */
    const HONORIFIC_PREFIX = 'honorificPrefix';
    /**
     * Honorific suffix
     *
     * @var string
     */
    const HONORIFIC_SUFFIX = 'honorificSuffix';
    /**
     * Sort string
     *
     * @var string
     */
    const SORT_STRING = 'sortString';
}
