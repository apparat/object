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

use Apparat\Object\Application\Factory\ObjectFactory;
use Apparat\Object\Application\Model\Object\Article;
use Apparat\Object\Domain\Factory\AuthorFactory;
use Apparat\Object\Domain\Model\Author\ApparatAuthor;
use Apparat\Object\Domain\Model\Object\AbstractObject;
use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Domain\Repository\Repository;
use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;
use Apparat\Object\Ports\Object;

/**
 * Object tests
 *
 * @package Apparat\Object
 * @subpackage ApparatTest
 */
class ObjectTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Example object path
     *
     * @var string
     */
    const OBJECT_PATH = '/2015/12/21/1.article/1';
    /**
     * Test repository
     *
     * @var Repository
     */
    protected static $repository = null;

    /**
     * Setup
     */
    public static function setUpBeforeClass()
    {
        \Apparat\Object\Ports\Repository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => __DIR__.DIRECTORY_SEPARATOR.'Fixture',
            ]
        );

        self::$repository = \Apparat\Object\Ports\Repository::instance(getenv('REPOSITORY_URL'));

        \date_default_timezone_set('UTC');
    }

    /**
     * Tears down the fixture
     */
    public function tearDown()
    {
        TestType::removeInvalidType();
        parent::tearDown();
    }

    /**
     * Test undefined object type
     *
     * @expectedException \Apparat\Object\Application\Factory\InvalidArgumentException
     * @expectedExceptionCode 1450905868
     */
    public function testUndefinedObjectType()
    {
        $resource = $this->getMock(ResourceInterface::class);
        $resource->method('getPropertyData')->willReturn([]);
        $repositoryPath = $this->getMockBuilder(RepositoryPath::class)->disableOriginalConstructor()->getMock();

        /** @var ResourceInterface $resource */
        /** @var RepositoryPath $repositoryPath */
        ObjectFactory::createFromResource($resource, $repositoryPath);
    }

    /**
     * Test invalid object type
     *
     * @expectedException \Apparat\Object\Domain\Model\Object\InvalidArgumentException
     * @expectedExceptionCode 1449871242
     */
    public function testInvalidObjectType()
    {
        $resource = $this->getMock(ResourceInterface::class);
        $resource->method('getPropertyData')->willReturn([SystemProperties::COLLECTION => ['type' => 'invalid']]);
        $articleObjectPath = new RepositoryPath(self::$repository, self::OBJECT_PATH);

        /** @var ResourceInterface $resource */
        ObjectFactory::createFromResource($resource, $articleObjectPath);
    }

    /**
     * Load an article object and test basic properties
     */
    public function testLoadArticleObject()
    {
        $articleObjectPath = new RepositoryPath(self::$repository, self::OBJECT_PATH);
        $articleObject = self::$repository->loadObject($articleObjectPath);
        $this->assertEquals(
            getenv('APPARAT_BASE_URL').getenv('REPOSITORY_URL').self::OBJECT_PATH,
            $articleObject->getAbsoluteUrl()
        );
    }

    /**
     * Load an article object and test its system properties
     */
    public function testLoadArticleObjectSystemProperties()
    {
        $articleObjectPath = new RepositoryPath(self::$repository, self::OBJECT_PATH);
        $articleObject = self::$repository->loadObject($articleObjectPath);
        $this->assertInstanceOf(Article::class, $articleObject);
        $this->assertEquals(new Id(1), $articleObject->getId());
        $this->assertEquals(new Type(Type::ARTICLE), $articleObject->getType());
        $this->assertEquals(new Revision(1), $articleObject->getRevision());
        $this->assertEquals(new \DateTimeImmutable('2015-12-21T22:30:00'), $articleObject->getCreated());
        $this->assertEquals(new \DateTimeImmutable('2015-12-21T22:45:00'), $articleObject->getPublished());
        $this->assertEquals('a123456789012345678901234567890123456789', $articleObject->getHash());
        $this->assertEquals(
            "# Example article object\n\nThis file is an example for an object of type `\"article\"`.\n",
            $articleObject->getPayload()
        );
    }

    /**
     * Load an article object and test its meta properties
     */
    public function testLoadArticleObjectMetaProperties()
    {
        $articleObjectPath = new RepositoryPath(self::$repository, self::OBJECT_PATH);
        $articleObject = self::$repository->loadObject($articleObjectPath);
        $this->assertInstanceOf(Article::class, $articleObject);
        $this->assertEquals('Example article object', $articleObject->getDescription());
        $this->assertEquals(
            'Article objects feature a Markdown payload along with some custom properties',
            $articleObject->getAbstract()
        );
        $this->assertArrayEquals(['apparat', 'object', 'example', 'article'], $articleObject->getKeywords());
        $this->assertArrayEquals(['example', 'text'], $articleObject->getCategories());

        $authorCount = count($articleObject->getAuthors());
        $articleObject->addAuthor(AuthorFactory::createFromString(AuthorTest::GENERIC_AUTHOR));
        $this->assertEquals($authorCount + 1, count($articleObject->getAuthors()));
    }

    /**
     * Load an article object and test its domain properties
     *
     * @expectedException \Apparat\Object\Domain\Model\Properties\InvalidArgumentException
     * @expectedExceptionCode 1450818168
     */
    public function testLoadArticleObjectDomainProperties()
    {
        $articleObjectPath = new RepositoryPath(self::$repository, self::OBJECT_PATH);
        $articleObject = self::$repository->loadObject($articleObjectPath);
        $this->assertEquals('/system/url', $articleObject->getDomainProperty('uid'));
        $this->assertEquals('value', $articleObject->getDomainProperty('group:single'));
        $articleObject->getDomainProperty('group:invalid');
    }

    /**
     * Load an article object and test an empty domain property name
     *
     * @expectedException \Apparat\Object\Domain\Model\Properties\InvalidArgumentException
     * @expectedExceptionCode 1450817720
     */
    public function testLoadArticleObjectDomainEmptyProperty()
    {
        $articleObjectPath = new RepositoryPath(self::$repository, self::OBJECT_PATH);
        $articleObject = self::$repository->loadObject($articleObjectPath);
        $articleObject->getDomainProperty('');
    }

    /**
     * Test the object facade with an absolute object URL
     */
    public function testObjectFacadeAbsolute()
    {
        $object = Object::instance(getenv('APPARAT_BASE_URL').getenv('REPOSITORY_URL').self::OBJECT_PATH);
        $this->assertInstanceOf(Article::class, $object);
    }

    /**
     * Test the object facade with a relative object URL
     */
    public function testObjectFacadeRelative()
    {
        $object = Object::instance(getenv('REPOSITORY_URL').self::OBJECT_PATH);
        $this->assertInstanceOf(Article::class, $object);
        foreach ($object->getAuthors() as $author) {
            if ($author instanceof ApparatAuthor) {
//				echo $author->getId()->getId();
            }
        }
    }

    /**
     * Test the object facade with an invalid relative object URL
     *
     * @expectedException \Apparat\Resource\Infrastructure\Io\File\InvalidArgumentException
     * @expectedExceptionCode 1447616824
     */
    public function testObjectFacadeRelativeInvalid()
    {
        $object = Object::instance(getenv('REPOSITORY_URL').'/2015/12/21/2.article/2');
        $this->assertInstanceOf(Article::class, $object);
    }

    /**
     * Test with a missing object type class
     *
     * @expectedException \Apparat\Object\Application\Factory\InvalidArgumentException
     * @expectedExceptionCode 1450824842
     */
    public function testInvalidObjectTypeClass()
    {
        TestType::addInvalidType();

        $resource = $this->getMock(ResourceInterface::class);
        $resource->method('getPropertyData')->willReturn([SystemProperties::COLLECTION => ['type' => 'invalid']]);
        $articleObjectPath = new RepositoryPath(self::$repository, '/2016/02/16/5.invalid/5');

        /** @var ResourceInterface $resource */
        ObjectFactory::createFromResource($resource, $articleObjectPath);
    }

    /**
     * Test instantiation of object with invalid domain properties collection
     *
     * @expectedException \Apparat\Object\Domain\Model\Properties\InvalidArgumentException
     * @expectedExceptionCode 1452288429
     */
    public function testInvalidDomainPropertyCollectionClass()
    {
        $this->getMockBuilder(AbstractObject::class)
            ->setConstructorArgs([new RepositoryPath(self::$repository, self::OBJECT_PATH)])
            ->getMock();
    }

    /**
     * Test the property data
     */
    public function testObjectPropertyData()
    {
//  $frontMarkResource = Resource::frontMark('file://'.__DIR__.DIRECTORY_SEPARATOR.'Fixture'.self::OBJECT_PATH.'.md');
        $object = Object::instance(getenv('REPOSITORY_URL').self::OBJECT_PATH);
        $this->assertTrue(is_array($object->getPropertyData()));
//        print_r($frontMarkResource->getData());
//        print_r($object->getPropertyData());
    }

    /**
     * Test the creation of an article object
     */
    public function testCreateArticleObject() {
//        $object = Object::create(Type::ARTICLE);
    }
}
