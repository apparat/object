<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Test
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

namespace Apparat\Object\Tests;

use Apparat\Object\Domain\Model\Properties\PropertiesInterface;
use Apparat\Object\Infrastructure\Model\Object\Object;

/**
 * System properties test
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class DomainPropertiesTest extends AbstractRepositoryEnabledTest
{
    /**
     * Example object path
     *
     * @var string
     */
    const ARTICLE_LOCATOR = '/2015/12/21/1-article/1';
    /**
     * Example object path
     *
     * @var string
     */
    const CONTACT_LOCATOR = '/2016/01/08/2-contact/2';
    /**
     * Property traversal separator
     *
     * @var string
     */
    const SEPARATOR = PropertiesInterface::PROPERTY_TRAVERSAL_SEPARATOR;

    /**
     * Test mutation by altering domain properties
     */
    public function testDomainPropertyMutation()
    {
        $object = Object::load(getenv('REPOSITORY_URL').self::ARTICLE_LOCATOR);
        $this->assertTrue(is_array($object->getPropertyData()));
        $objectUrl = $object->getAbsoluteUrl();
        $objectRevision = $object->getRevision();
        $object->setDomainProperty('a'.self::SEPARATOR.'b'.self::SEPARATOR.'c', 'mutated');
        $this->assertEquals(preg_replace('%\/(.?+)$%', '/.$1-2', $objectUrl), $object->getAbsoluteUrl());
        $this->assertEquals($objectRevision->getRevision() + 1, $object->getRevision()->getRevision());
        $this->assertTrue($object->hasBeenModified());
        $this->assertTrue($object->hasBeenMutated());
    }

    /**
     * Test a domain property model violation
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     * @expectedExceptionCode 1465130534
     */
    public function testDomainPropertyModel()
    {
        $object = Object::load(getenv('REPOSITORY_URL').self::CONTACT_LOCATOR);
        $object->setDomainProperty('givenName', 'apparat');
        $object->setDomainProperty('givenName'.self::SEPARATOR.'subproperty', 'violation');
    }

    /**
     * Test a domain property model violation
     */
    public function testContactDomainPropertyModel()
    {
        $object = Object::load(getenv('REPOSITORY_URL').self::CONTACT_LOCATOR);
        $object->setDomainProperty('givenName', 'John');
        $object->setDomainProperty('givenName', 'John'); // Intentional re-set!
        $object->setDomainProperty('familyName', 'Doe');
        $object->setDomainProperty('nickname', 'Houdini');
        $object->setDomainProperty('additionalName', 'Mike');
        $object->setDomainProperty('honorificPrefix', 'Prof.');
        $object->setDomainProperty('honorificSuffix', 'jr.');
        $object->setDomainProperty('email', ['john.doe@example.com', 'john@test.com']);
        $object->setDomainProperty('logo', ['logo.jpg', '/2016/06/05/2-image']);
        $object->setDomainProperty('org', '/2016/06/05/2-contact');
        $object->setDomainProperty('bday', '2016-06-05T00:00:00Z');
//        print_r($object->getPropertyData());
    }
}
