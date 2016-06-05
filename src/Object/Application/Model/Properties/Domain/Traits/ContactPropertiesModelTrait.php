<?php

/**
 * apparat/object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Application\Model\Properties\Domain\Traits
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

namespace Apparat\Object\Application\Model\Properties\Domain\Traits;

use Apparat\Object\Application\Model\Properties\Datatype\ApparatUrl;
use Apparat\Object\Application\Model\Properties\Datatype\Datetime;
use Apparat\Object\Application\Model\Properties\Datatype\Email;
use Apparat\Object\Application\Model\Properties\Datatype\Sentence;
use Apparat\Object\Application\Model\Properties\Datatype\Text;
use Apparat\Object\Application\Model\Properties\Datatype\Token;
use Apparat\Object\Application\Model\Properties\Datatype\Url;
use Apparat\Object\Application\Model\Properties\Domain\Contact;
use Apparat\Object\Domain\Contract\ObjectTypesInterface;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\InvalidArgumentException;

/**
 * Contact properties model trait
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Application
 * @method ObjectInterface getObject()
 */
trait ContactPropertiesModelTrait
{
    /**
     * Use the address properties model
     */
    use AddressPropertiesModelTrait;
    /**
     * Property model: Name
     *
     * @var array
     */
    protected $pmName = [false, [Sentence::class]];
    /**
     * Property model: Given name
     *
     * @var array
     */
    protected $pmGivenName = [false, [Sentence::class]];
    /**
     * Property model: Additional name
     *
     * @var array
     */
    protected $pmAdditionalName = [false, [Sentence::class]];
    /**
     * Property model: Family name
     *
     * @var array
     */
    protected $pmFamilyName = [false, [Sentence::class]];
    /**
     * Property model: Nickname
     *
     * @var array
     */
    protected $pmNickname = [false, [Sentence::class]];
    /**
     * Property model: Sort string
     *
     * @var array
     */
    protected $pmSortString = [false, [Sentence::class]];
    /**
     * Property model: Honorific prefix
     *
     * @var array
     */
    protected $pmHonorificPrefix = [false, [Sentence::class]];
    /**
     * Property model: Honorific suffix
     *
     * @var array
     */
    protected $pmHonorificSuffix = [false, [Sentence::class]];
    /**
     * Property model: Email
     *
     * @var array
     */
    protected $pmEmail = [true, [Email::class]];
    /**
     * Property model: Logo
     *
     * @var array
     */
    protected $pmLogo = [true, [ApparatUrl::class, Url::class]];
    /**
     * Property model: URL
     *
     * @var array
     */
    protected $pmUrl = [true, [Url::class]];
    /**
     * Property model: Address
     *
     * @var array
     */
    protected $pmAdr = [true, [ApparatUrl::class, Text::class]];
    /**
     * Property model: Telephone number
     *
     * @var array
     */
    protected $pmTel = [true, [Sentence::class]];
    /**
     * Property model: Note
     *
     * @var array
     */
    protected $pmNote = [true, [Text::class]];
    /**
     * Property model: Birthday
     *
     * @var array
     */
    protected $pmBday = [false, [Datetime::class]];
    /**
     * Property model: Cryptographic public key
     *
     * @var array
     */
    protected $pmKey = [true, [Url::class]];
    /**
     * Property model: Organization
     *
     * @var array
     */
    protected $pmOrg = [
        true,
        [ApparatUrl::class, Sentence::class],
        [ApparatUrl::class => [ObjectTypesInterface::CONTACT]]
    ];
    /**
     * Property model: Job title
     *
     * @var array
     */
    protected $pmJobTitle = [true, [Sentence::class]];
    /**
     * Property model: Instant messaging
     *
     * @var array
     */
    protected $pmImpp = [true, [Url::class]];
    /**
     * Property model: Biological sex
     *
     * @var array
     */
    protected $pmSex = [false, [Token::class]];
    /**
     * Property model: Gender identity
     *
     * @var array
     */
    protected $pmGenderIdentity = [false, [Sentence::class]];
    /**
     * Property model: Anniversary
     *
     * @var array
     */
    protected $pmAnniversary = [true, [Datetime::class]];

    /**
     * Set the given name
     *
     * @param string $property Property
     * @param string $value Given name
     */
    public function setPmGivenName(&$property, $value)
    {
        $property = $value;
        $this->composeFullName(Contact::GIVEN_NAME, $value);
    }

    /**
     * Set the nickname
     *
     * @param string $property Property
     * @param string $value Nickname
     */
    public function setPmNickname(&$property, $value)
    {
        $property = $value;
        $this->composeFullName(Contact::NICKNAME, $value);
    }

    /**
     * Set the additional name
     *
     * @param string $property Property
     * @param string $value Additional name
     */
    public function setPmAdditionalName(&$property, $value)
    {
        $property = $value;
        $this->composeFullName(Contact::ADDITIONAL_NAME, $value);
    }

    /**
     * Set the given name
     *
     * @param string $property Property
     * @param string $value Given name
     */
    public function setPmFamilyName(&$property, $value)
    {
        $property = $value;
        $this->composeFullName(Contact::FAMILY_NAME, $value);
    }

    /**
     * Set the honorific prefix
     *
     * @param string $property Property
     * @param string $value Honorific prefix
     */
    public function setPmHonorificPrefix(&$property, $value)
    {
        $property = $value;
        $this->composeFullName(Contact::HONORIFIC_PREFIX, $value);
    }

    /**
     * Set the honorific suffix
     *
     * @param string $property Property
     * @param string $value Honorific suffix
     */
    public function setPmHonorificSuffix(&$property, $value)
    {
        $property = $value;
        $this->composeFullName(Contact::HONORIFIC_SUFFIX, $value);
    }

    /**
     * Set the full contact name
     *
     * @param string $propertyName Current name property
     * @param string $propertyValue Current name property value
     */
    protected function composeFullName($propertyName, $propertyValue)
    {
        $nameProperties = [
            Contact::HONORIFIC_PREFIX => '',
            Contact::GIVEN_NAME => '',
            Contact::NICKNAME => '',
            Contact::ADDITIONAL_NAME => '',
            Contact::FAMILY_NAME => '',
            Contact::HONORIFIC_SUFFIX => ''
        ];
        foreach (array_keys($nameProperties) as $name) {
            try {
                $nameProperty = trim($this->getObject()->getDomainProperty($name));
                if (strlen($nameProperty)) {
                    $nameProperties[$name] = $nameProperty;
                }
            } catch (InvalidArgumentException $e) {
                continue;
            }
        }
        $nameProperties[$propertyName] = $propertyValue;
        if (!empty($nameProperties[Contact::NICKNAME])) {
            $nameProperties[Contact::NICKNAME] = '"'.$nameProperties[Contact::NICKNAME].'"';
        }
        $this->getObject()->setTitle(implode(' ', array_filter($nameProperties)));
    }
}