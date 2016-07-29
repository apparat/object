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
use Apparat\Object\Domain\Model\Object\AbstractObject;
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Domain\Model\Uri\RepositoryLocator;
use Apparat\Object\Infrastructure\Factory\AdapterStrategyFactory;
use Apparat\Object\Infrastructure\Model\Object\Object;
use Apparat\Object\Infrastructure\Repository\Repository as InfrastructureRepository;
use Apparat\Object\Ports\Types\Object as ObjectTypes;

/**
 * Object tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Test
 */
class ObjectTest extends AbstractRepositoryEnabledTest
{
    /**
     * Example object locator
     *
     * @var string
     */
    const OBJECT_LOCATOR = '/2015/12/21/1-article/1';
    /**
     * Example hidden object locator
     *
     * @var string
     */
    const HIDDEN_OBJECT_LOCATOR = '/2016/05/26/6-article/6';

    /**
     * Tears down the fixture
     */
    public function tearDown()
    {
        TestTypeService::removeInvalidType();
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
        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getPropertyData')->willReturn([]);
        $repositoryLocator =
            $this->getMockBuilder(RepositoryLocator::class)->disableOriginalConstructor()->getMock();

        /** @var ResourceInterface $resource */
        /** @var RepositoryLocator $repositoryLocator */
        ObjectFactory::createFromResource($repositoryLocator, $resource);
    }

    /**
     * Test invalid object type
     *
     * @expectedException \Apparat\Object\Domain\Model\Object\InvalidArgumentException
     * @expectedExceptionCode 1449871242
     */
    public function testInvalidObjectType()
    {
        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getPropertyData')->willReturn([SystemProperties::COLLECTION => ['type' => 'invalid']]);
        $articleObjectLocator = new RepositoryLocator(self::$repository, self::OBJECT_LOCATOR);

        /** @var ResourceInterface $resource */
        ObjectFactory::createFromResource($articleObjectLocator, $resource);
    }

    /**
     * Load an article object with an invalid visibility requirement
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     */
    public function testLoadObjectInvalidVisibility()
    {
        $articleObjectLocator = new RepositoryLocator(self::$repository, self::OBJECT_LOCATOR);
        self::$repository->loadObject($articleObjectLocator, 0);
    }

    /**
     * Load a non-existing object
     *
     * @expectedException \Apparat\Object\Application\Model\Object\InvalidArgumentException
     * @expectedExceptionCode 1466882391
     */
    public function testLoadNonExistingObject()
    {
        AdapterStrategyFactory::setAdapterStrategyTypeClass(
            TestFileAdapterStrategy::TYPE,
            TestFileAdapterStrategy::class
        );
        $repository = InfrastructureRepository::register(
            'non-'.getenv('REPOSITORY_URL'),
            [
                'type' => TestFileAdapterStrategy::TYPE,
                'root' => __DIR__.DIRECTORY_SEPARATOR.'Fixture',
            ]
        );
        $objectLocator = new RepositoryLocator($repository, self::OBJECT_LOCATOR);
        $repository->loadObject($objectLocator);
    }

    /**
     * Test the object facade with an absolute object URL
     */
    public function testObjectAbsolute()
    {
        $object = Object::load(getenv('APPARAT_BASE_URL').getenv('REPOSITORY_URL').self::OBJECT_LOCATOR);
        $this->assertInstanceOf(Article::class, $object);
    }

    /**
     * Test the object facade with a relative object URL
     */
    public function testObjectRelative()
    {
        $object = Object::load(getenv('REPOSITORY_URL').self::OBJECT_LOCATOR);
        $this->assertInstanceOf(Article::class, $object);
    }

    /**
     * Test the object facade with an invalid relative object URL
     *
     * @expectedException \Apparat\Resource\Ports\InvalidReaderArgumentException
     * @expectedExceptionCode 1447616824
     */
    public function testObjectRelativeInvalid()
    {
        $object = Object::load(getenv('REPOSITORY_URL').'/2015/12/21/2-article/2');
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
        TestTypeService::addInvalidType();

        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getPropertyData')->willReturn([SystemProperties::COLLECTION => ['type' => 'invalid']]);
        $articleObjectLocator = new RepositoryLocator(self::$repository, '/2016/02/16/5-invalid/5');

        /** @var ResourceInterface $resource */
        ObjectFactory::createFromResource($articleObjectLocator, $resource);
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
            ->setConstructorArgs([new RepositoryLocator(self::$repository, self::OBJECT_LOCATOR)])
            ->getMock();
    }

    /**
     * Test the property data
     */
    public function testObjectPropertyData()
    {
//            $frontMarkResource =
//                Resource::frontMark('file://'.__DIR__.DIRECTORY_SEPARATOR.'Fixture'.self::OBJECT_LOCATOR.'.md');
        $object = Object::load(getenv('REPOSITORY_URL').self::OBJECT_LOCATOR);
        $this->assertTrue(is_array($object->getPropertyData()));
//            print_r($frontMarkResource->getData());
//            print_r($object->getPropertyData());
    }

    /**
     * Test mutation by altering metadata
     *
     * @expectedException \Apparat\Object\Domain\Model\Properties\OutOfBoundsException
     * @expectedExceptionCode 1462632083
     */
    public function testMetaDataMutation()
    {
        $object = Object::load(getenv('REPOSITORY_URL').self::OBJECT_LOCATOR);
        $this->assertTrue(is_array($object->getPropertyData()));
        $objectUrl = $object->getAbsoluteUrl();
        $objectRevision = $object->getRevision();
        $object->setTitle($object->getTitle().' (mutated)');
        $object->setSlug($object->getSlug().'-mutated');
        $object->setDescription($object->getDescription().' (mutated)');
        $object->setAbstract($object->getAbstract());
        $object->setLicense(ltrim($object->getLicense().', ', ', ').'MIT');
        $object->setKeywords(array_merge($object->getKeywords(), ['mutated']));
        $object->setCategories($object->getCategories());
        $this->assertEquals(preg_replace('%\/(.?+)$%', '/.$1-2', $objectUrl), $object->getAbsoluteUrl());
        $this->assertEquals($objectRevision->getRevision() + 1, $object->getRevision()->getRevision());
        $this->assertTrue($object->hasBeenModified());
        $this->assertTrue($object->hasBeenMutated());
        $this->assertEquals('MIT', $object->getLicense());
        $this->assertEquals(ObjectTypes::PRIVACY_PRIVATE, $object->getPrivacy());
        $this->assertEquals(
            ObjectTypes::PRIVACY_PUBLIC,
            $object->setPrivacy(ObjectTypes::PRIVACY_PUBLIC)->getPrivacy()
        );
        $object->setPrivacy('invalid');
    }

    /**
     * Test change by altering relations
     */
    public function testRelationChange()
    {
        // TODO: Implement
    }

    /**
     * Test to persist an earlier revision
     */
    public function testPersistEarlierRevision()
    {
        // TODO
    }
}
