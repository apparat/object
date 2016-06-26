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

use Apparat\Object\Application\Model\Properties\Domain\Contact as ContactProperties;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Properties\AbstractProperties;
use Apparat\Object\Domain\Model\Properties\MetaProperties;
use Apparat\Object\Domain\Model\Properties\Relations;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Infrastructure\Model\Object\Apparat\AbstractApparatObject;
use Apparat\Object\Ports\Types\Object;
use Apparat\Object\Ports\Types\Relation;

/**
 * Apparat article
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Infrastructure
 * @method string getName() Return the contact name
 * @method string getHonorificPrefix() Return the contact honorific prefix
 * @method string getGivenName() Return the contact given name
 * @method string getAdditionalName() Return the contact additional name
 * @method string getFamilyName() Return the contact family name
 * @method string getSortString() Return the contact sort string
 * @method string getHonorificSuffix() Return the contact honorific suffix
 * @method string getNickname() Return the contact nickname
 * @method string getEmail() Return the contact email address
 * @method string getLogo() Return the contact logo
 * @method string getPhoto() Return the contact photo
 * @method string getAdr() Return the contact postal address
 * @method string getPostOfficeBox() Return the contact post office box
 * @method string getExtendedAddress() Return the contact extended address
 * @method string getStreetAddress() Return the contact street address
 * @method string getLocality() Return the contact locality
 * @method string getRegion() Return the contact region
 * @method string getPostalCode() Return the contact postal code
 * @method string getCountryName() Return the contact country name
 * @method string getLabel() Return the contact address label
 * @method string getGeo() Return the contact geo data
 * @method string getLatitude() Return the contact latitude
 * @method string getLongitude() Return the contact longitude
 * @method string getAltitude() Return the contact altitude
 * @method string getTel() Return the contact telephone number
 * @method string getNote() Return the contact notes
 * @method string getBday() Return the contact birthday
 * @method string getKey() Return the contact cryptographic key
 * @method string getOrg() Return the contact organization
 * @method string getJobTitle() Return the contact job title
 * @method string getRole() Return the contact role
 * @method string getImpp() Return the contact instant messenger profile
 * @method string getSex() Return the contact sex
 * @method string getGenderIdentity() Return the contact gender identity
 * @method string getAnniversary() Return the contact anniversaries
 */
class Contact extends AbstractApparatObject
{
    /**
     * Object type
     *
     * @var string
     */
    const TYPE = Object::CONTACT;
    /**
     * Property mapping
     *
     * @var array
     */
    protected $mapping = [
        ContactProperties::PUBLISHED => SystemProperties::PROPERTY_PUBLISHED,
        ContactProperties::UPDATED => SystemProperties::PROPERTY_MODIFIED,
        ContactProperties::AUTHOR => [Relations::COLLECTION, Relation::CONTRIBUTED_BY],
        ContactProperties::CATEGORY => MetaProperties::PROPERTY_CATEGORIES,
        ContactProperties::URL => AbstractProperties::PROPERTY_ABSOLUTE_URL,
        ContactProperties::UID => AbstractProperties::PROPERTY_CANONICAL_URL,
        ContactProperties::LOCATION => SystemProperties::PROPERTY_LOCATION,
        ContactProperties::SYNDICATION => [Relations::COLLECTION, Relation::SYNDICATED_TO],

        ContactProperties::NAME => MetaProperties::PROPERTY_TITLE,
        ContactProperties::HONORIFIC_PREFIX => [ContactProperties::COLLECTION, ContactProperties::HONORIFIC_PREFIX],
        ContactProperties::GIVEN_NAME => [ContactProperties::COLLECTION, ContactProperties::GIVEN_NAME],
        ContactProperties::ADDITIONAL_NAME => [ContactProperties::COLLECTION, ContactProperties::ADDITIONAL_NAME],
        ContactProperties::FAMILY_NAME => [ContactProperties::COLLECTION, ContactProperties::FAMILY_NAME],
        ContactProperties::SORT_STRING => [ContactProperties::COLLECTION, ContactProperties::SORT_STRING],
        ContactProperties::HONORIFIC_SUFFIX => [ContactProperties::COLLECTION, ContactProperties::HONORIFIC_SUFFIX],
        ContactProperties::NICKNAME => [ContactProperties::COLLECTION, ContactProperties::NICKNAME],
        ContactProperties::EMAIL => [ContactProperties::COLLECTION, ContactProperties::EMAIL],
        ContactProperties::LOGO => [ContactProperties::COLLECTION, ContactProperties::LOGO],
        ContactProperties::PHOTO => [ContactProperties::COLLECTION, ContactProperties::PHOTO],
        ContactProperties::ADR => [ContactProperties::COLLECTION, ContactProperties::ADR],
        ContactProperties::POST_OFFICE_BOX => [ContactProperties::COLLECTION, ContactProperties::POST_OFFICE_BOX],
        ContactProperties::EXTENDED_ADDRESS => [ContactProperties::COLLECTION, ContactProperties::EXTENDED_ADDRESS],
        ContactProperties::STREET_ADDRESS => [ContactProperties::COLLECTION, ContactProperties::STREET_ADDRESS],
        ContactProperties::LOCALITY => [ContactProperties::COLLECTION, ContactProperties::LOCALITY],
        ContactProperties::REGION => [ContactProperties::COLLECTION, ContactProperties::REGION],
        ContactProperties::POSTAL_CODE => [ContactProperties::COLLECTION, ContactProperties::POSTAL_CODE],
        ContactProperties::COUNTRY_NAME => [ContactProperties::COLLECTION, ContactProperties::COUNTRY_NAME],
        ContactProperties::LABEL => ObjectInterface::PROPERTY_PAYLOAD,
        ContactProperties::GEO => [ContactProperties::COLLECTION, ContactProperties::GEO],
        ContactProperties::LATITUDE => [ContactProperties::COLLECTION, ContactProperties::LATITUDE],
        ContactProperties::LONGITUDE => [ContactProperties::COLLECTION, ContactProperties::LONGITUDE],
        ContactProperties::ALTITUDE => [ContactProperties::COLLECTION, ContactProperties::ALTITUDE],
        ContactProperties::TEL => [ContactProperties::COLLECTION, ContactProperties::TEL],
        ContactProperties::NOTE => [ContactProperties::COLLECTION, ContactProperties::NOTE],
        ContactProperties::BDAY => [ContactProperties::COLLECTION, ContactProperties::BDAY],
        ContactProperties::KEY => [ContactProperties::COLLECTION, ContactProperties::KEY],
        ContactProperties::ORG => [ContactProperties::COLLECTION, ContactProperties::ORG],
        ContactProperties::JOB_TITLE => [ContactProperties::COLLECTION, ContactProperties::JOB_TITLE],
        ContactProperties::ROLE => [ContactProperties::COLLECTION, ContactProperties::ROLE],
        ContactProperties::IMPP => [ContactProperties::COLLECTION, ContactProperties::IMPP],
        ContactProperties::SEX => [ContactProperties::COLLECTION, ContactProperties::SEX],
        ContactProperties::GENDER_IDENTITY => [ContactProperties::COLLECTION, ContactProperties::GENDER_IDENTITY],
        ContactProperties::ANNIVERSARY => [ContactProperties::COLLECTION, ContactProperties::ANNIVERSARY],
    ];
}
