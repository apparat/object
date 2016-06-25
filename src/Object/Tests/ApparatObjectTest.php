<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Infrastructure
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

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Application\Model\Object\Article as ApplicationArticle;
use Apparat\Object\Infrastructure\Model\Object\Object;
use Apparat\Object\Ports\Facades\RepositoryFacade;
use Apparat\Object\Ports\Factory\SelectorFactory;
use Apparat\Object\Ports\Object\Article;
use Apparat\Object\Ports\Object\Contact;

/**
 * Object URL tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Test
 */
class ApparatObjectTest extends AbstractRepositoryEnabledTest
{
    /**
     * Example article locator
     *
     * @var string
     */
    const ARTICLE_LOCATOR = '/repo/2015/12/21/1-article/1';
    /**
     * Example contact locator
     *
     * @var string
     */
    const CONTACT_LOCATOR = '/repo/2016/01/08/2-contact/2';

    /**
     * Test the apparat object factory with an invalid object type
     *
     * @expectedException \Apparat\Object\Ports\Exceptions\InvalidArgumentException
     * @expectedExceptionCode 1465368597
     */
    public function testApparatObjectFactoryInvalidType()
    {
        /** @var ApplicationArticle $articleObj */
        $articleObj = Object::load(self::ARTICLE_LOCATOR);
        TestApparatObjectFactory::create($articleObj);
    }

    /**
     * Test the selection of apparat objects via the repository facade
     */
    public function testApparatObjectFindCollection()
    {
        $selector = SelectorFactory::createFromString('/2015/*/*/*-article');
        $apparatObjects = RepositoryFacade::instance(getenv('REPOSITORY_URL'))->findObjects($selector);
        $this->assertTrue(is_array($apparatObjects));
        foreach ($apparatObjects as $apparatObject) {
            $this->assertInstanceOf(Article::class, $apparatObject);
        }
        $this->assertEquals(
            count($apparatObjects),
            count(RepositoryFacade::instance(getenv('REPOSITORY_URL'))->findObjects('/2015/*/*/*-article'))
        );
    }

    /**
     * Test the article apparat object with an illegal setter
     *
     * @expectedException \Apparat\Object\Ports\Exceptions\InvalidArgumentException
     * @expectedExceptionCode 1466804125
     */
    public function testArticleApparatObjectIllegalSetter()
    {
        /** @var Article $articleApparatObj */
        $articleApparatObj = RepositoryFacade::instance('repo')->loadObject(self::ARTICLE_LOCATOR);
        $this->assertInstanceOf(Article::class, $articleApparatObj);

        $this->assertTrue(isset($articleApparatObj['name']));
        $this->assertEquals('First repository article', $articleApparatObj['name']);
        $this->assertEquals('First repository article', $articleApparatObj->getName());
        $articleApparatObj['name'] = null;
    }

    /**
     * Test the article apparat object with an invalid getter
     *
     * @expectedException \BadMethodCallException
     */
    public function testArticleApparatObjectInvalidGetter()
    {
        /** @var Article $articleApparatObj */
        $articleApparatObj = RepositoryFacade::instance('repo')->loadObject(self::ARTICLE_LOCATOR);
        $this->assertInstanceOf(Article::class, $articleApparatObj);

        // Test serialization / deserialization
        $serialized = serialize($articleApparatObj);
        $unserializedArticle = unserialize($serialized);
        $this->assertEquals($unserializedArticle['name'], $articleApparatObj['name']);

        // Test iteration
        $articleArray = $articleApparatObj->getArrayCopy();
        $this->assertEquals(count($articleArray), count($articleApparatObj));
        foreach ($articleApparatObj as $property => $value) {
            $this->assertTrue(array_key_exists($property, $articleArray));
            $this->assertEquals($articleArray[$property], $value);
            unset($articleArray[$property]);
        }
        $this->assertEquals(0, count($articleArray));

        /** @noinspection PhpUndefinedMethodInspection */
        $articleApparatObj->getInvalid();
    }

    /**
     * Test the article apparat object with an invalid array getter
     *
     * @expectedException \Apparat\Object\Ports\Exceptions\InvalidArgumentException
     * @expectedExceptionCode 1465330399
     */
    public function testArticleApparatObjectInvalidArrayGetter()
    {
        /** @var Article $articleApparatObj */
        $articleApparatObj = RepositoryFacade::instance('repo')->loadObject(self::ARTICLE_LOCATOR);
        $this->assertInstanceOf(Article::class, $articleApparatObj);

        $articleApparatObj['invalid'];
    }

    /**
     * Test the article apparat object with an invalid array append
     *
     * @expectedException \Apparat\Object\Ports\Exceptions\InvalidArgumentException
     * @expectedExceptionCode 1466804193
     */
    public function testArticleApparatObjectInvalidArrayAppend()
    {
        /** @var Article $articleApparatObj */
        $articleApparatObj = RepositoryFacade::instance('repo')->loadObject(self::ARTICLE_LOCATOR);
        $this->assertInstanceOf(Article::class, $articleApparatObj);

        $articleApparatObj->append('invalid');
    }

    /**
     * Test sorting and exchange methods of an article apparat object
     *
     * @expectedException \Apparat\Object\Ports\Exceptions\InvalidArgumentException
     * @expectedExceptionCode 1466805183
     */
    public function testArticleApparatObjectSortingExchange()
    {
        /** @var Article $articleApparatObj */
        $articleApparatObj = RepositoryFacade::instance('repo')->loadObject(self::ARTICLE_LOCATOR);
        $this->assertInstanceOf(Article::class, $articleApparatObj);

        $articleApparatObj->asort();
        $articleApparatObj->ksort();
        $articleApparatObj->uasort(function () {
        });
        $articleApparatObj->uksort(function () {
        });
        $articleApparatObj->natsort();
        $articleApparatObj->natcasesort();

        /** @var ApplicationArticle $articleObj */
        $articleObj = Object::load(self::ARTICLE_LOCATOR);
        $articleApparatObj->exchangeArray($articleObj);
        $articleApparatObj->exchangeArray(new \stdClass());
    }

    /**
     * Test illegal unsetting of apparat object property
     *
     * @expectedException \Apparat\Object\Ports\Exceptions\InvalidArgumentException
     * @expectedExceptionCode 1465330565
     */
    public function testArticleApparatObjectUnvalidUnset()
    {
        /** @var Article $articleApparatObj */
        $articleApparatObj = RepositoryFacade::instance('repo')->loadObject(self::ARTICLE_LOCATOR);
        unset($articleApparatObj['name']);
    }

    /**
     * Test invalid apparat object getter
     *
     * @expectedException \Apparat\Object\Ports\Exceptions\InvalidArgumentException
     * @expectedExceptionCode 1465330399
     */
    public function testInvalidApparatObjectGetter()
    {
        /** @var ApplicationArticle $articleObj */
        $articleObj = Object::load(self::ARTICLE_LOCATOR);
        $articleApparatObj = Kernel::create(TestApparatObject::class, [$articleObj]);
        $articleApparatObj['invalid'];
    }

    /**
     * Test the contact object
     */
    public function testContactObject()
    {
        $contactApparatObject = RepositoryFacade::instance('repo')->loadObject(self::CONTACT_LOCATOR);
        $this->assertInstanceOf(Contact::class, $contactApparatObject);
    }
}
