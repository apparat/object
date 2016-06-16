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

namespace Apparat\Object\Tests {

    use Apparat\Kernel\Ports\Kernel;
    use Apparat\Object\Application\Factory\ObjectFactory;
    use Apparat\Object\Application\Model\Object\Article;
    use Apparat\Object\Application\Service\TypeService;
    use Apparat\Object\Domain\Model\Object\AbstractObject;
    use Apparat\Object\Domain\Model\Object\Id;
    use Apparat\Object\Domain\Model\Object\ResourceInterface;
    use Apparat\Object\Domain\Model\Object\Revision;
    use Apparat\Object\Domain\Model\Object\Type;
    use Apparat\Object\Domain\Model\Path\RepositoryPath;
    use Apparat\Object\Domain\Model\Properties\MetaProperties;
    use Apparat\Object\Domain\Model\Properties\SystemProperties;
    use Apparat\Object\Domain\Repository\Repository;
    use Apparat\Object\Infrastructure\Model\Object\Object;
    use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;
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
         * Default privacy
         *
         * @var string
         */
        protected static $defaultPrivacy;
        /**
         * Example object path
         *
         * @var string
         */
        const OBJECT_PATH = '/2015/12/21/1-article/1';
        /**
         * Example hidden object path
         *
         * @var string
         */
        const HIDDEN_OBJECT_PATH = '/2016/05/26/6-article/6';

        /**
         * Setup
         */
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            self::$defaultPrivacy = getenv('OBJECT_DEFAULT_PRIVACY');

            TypeService::enableType(Type::ARTICLE);
            TypeService::enableType(Type::CONTACT);
            TypeService::enableType(Type::GEO);
            TypeService::enableType(Type::IMAGE);
            TypeService::enableType(Type::NOTE);
        }

        /**
         * Tears down the fixture
         */
        public function tearDown()
        {
            putenv('MOCK_FLOCK');
            putenv('MOCK_RENAME');
            putenv('OBJECT_DEFAULT_PRIVACY='.self::$defaultPrivacy);
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
            $repositoryPath = $this->getMockBuilder(RepositoryPath::class)->disableOriginalConstructor()->getMock();

            /** @var ResourceInterface $resource */
            /** @var RepositoryPath $repositoryPath */
            ObjectFactory::createFromResource($repositoryPath, $resource);
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
            $articleObjectPath = new RepositoryPath(self::$repository, self::OBJECT_PATH);

            /** @var ResourceInterface $resource */
            ObjectFactory::createFromResource($articleObjectPath, $resource);
        }

        /**
         * Load an article object with an invalid visibility requirement
         *
         * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
         * @expectedExceptionCode 1449999646
         */
        public function testLoadArticleObjectInvalidVisibility()
        {
            $articleObjectPath = new RepositoryPath(self::$repository, self::OBJECT_PATH);
            self::$repository->loadObject($articleObjectPath, 0);
        }

        /**
         * Load an article object
         *
         * @expectedException \Apparat\Object\Domain\Model\Object\OutOfBoundsException
         * @expectedExceptionCode 1461619783
         */
        public function testLoadArticleObject()
        {
            $articleObjectPath = new RepositoryPath(self::$repository, self::OBJECT_PATH);
            $articleObject = self::$repository->loadObject($articleObjectPath);
            $this->assertEquals(
                getenv('APPARAT_BASE_URL').getenv('REPOSITORY_URL').self::OBJECT_PATH,
                $articleObject->getAbsoluteUrl()
            );
            $this->assertFalse($articleObject->isDeleted());
            $this->assertFalse($articleObject->getRepositoryPath()->isHidden());

            /** @var Revision $invalidRevision */
            $invalidRevision = Kernel::create(Revision::class, [99]);
            $articleObject->useRevision($invalidRevision);
        }

        /**
         * Load a hidden article object
         */
        public function testLoadHiddenArticleObject()
        {
            $articleObjectPath = new RepositoryPath(self::$repository, self::HIDDEN_OBJECT_PATH);
            $articleObject = self::$repository->loadObject($articleObjectPath);
            $this->assertTrue($articleObject->isDeleted());
            $this->assertTrue($articleObject->getRepositoryPath()->isHidden());
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
            $this->assertEquals(Kernel::create(Type::class, [ObjectTypes::ARTICLE]), $articleObject->getType());
            $this->assertEquals(new Revision(1), $articleObject->getRevision());
            $this->assertFalse($articleObject->isDraft());
            $this->assertTrue($articleObject->isPublished());
            $this->assertEquals(new \DateTimeImmutable('2015-12-21T22:30:00'), $articleObject->getCreated());
            $this->assertEquals(new \DateTimeImmutable('2015-12-21T22:45:00'), $articleObject->getPublished());
            $this->assertNull($articleObject->getDeleted());
            $this->assertEquals('en', $articleObject->getLanguage());
            $this->assertEquals(
                "# Example article object\n\nThis file is an example for an object of type `\"article\"`. ".
                "It has a link to [Joschi Kuphal's website](https://jkphl.is) and features his avatar:\n".
                "![Joschi Kuphal](https://jkphl.is/avatar.jpg)",
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

            // TODO Replace with contributed-by relations
//            $authorCount = count($articleObject->getAuthors());
//            $articleObject->addAuthor(AuthorFactory::createFromString(AuthorTest::GENERIC_AUTHOR));
//            $this->assertEquals($authorCount + 1, count($articleObject->getAuthors()));
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
        public function testObjectAbsolute()
        {
            $object = Object::load(getenv('APPARAT_BASE_URL').getenv('REPOSITORY_URL').self::OBJECT_PATH);
            $this->assertInstanceOf(Article::class, $object);
        }

        /**
         * Test the object facade with a relative object URL
         */
        public function testObjectRelative()
        {
            $object = Object::load(getenv('REPOSITORY_URL').self::OBJECT_PATH);
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
            $articleObjectPath = new RepositoryPath(self::$repository, '/2016/02/16/5-invalid/5');

            /** @var ResourceInterface $resource */
            ObjectFactory::createFromResource($articleObjectPath, $resource);
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
            $object = Object::load(getenv('REPOSITORY_URL').self::OBJECT_PATH);
            $this->assertTrue(is_array($object->getPropertyData()));
//        print_r($frontMarkResource->getData());
//        print_r($object->getPropertyData());
        }

        /**
         * Test mutation by altering metadata
         *
         * @expectedException \Apparat\Object\Domain\Model\Properties\OutOfBoundsException
         * @expectedExceptionCode 1462632083
         */
        public function testMetaDataMutation()
        {
            $object = Object::load(getenv('REPOSITORY_URL').self::OBJECT_PATH);
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

        /**
         * Test the creation and persisting of an article object with failing file lock
         *
         * @expectedException \Apparat\Object\Domain\Repository\RuntimeException
         * @expectedExceptionCode 1461406873
         */
        public function testCreateArticleObjectLockingImpossible()
        {
            putenv('MOCK_FLOCK=1');
            $this->testCreateAndPublishArticleObject();
        }

        /**
         * Test the creation and persisting of an article object
         *
         * @expectedException \Apparat\Object\Domain\Model\Object\RuntimeException
         * @expectedExceptionCode 1462124874
         */
        public function testCreateAndPublishArticleObject()
        {
            putenv('OBJECT_DEFAULT_PRIVACY=public');

            // Create a temporary repository & article
            $tempRepoDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo';
            $payload = 'Revision 1 draft';
            $creationDate = new \DateTimeImmutable('yesterday');
            $article = $this->createRepositoryAndArticleObject($tempRepoDirectory, $payload, $creationDate);
            $this->assertInstanceOf(Article::class, $article);
            $this->assertEquals(MetaProperties::PRIVACY_PUBLIC, $article->getPrivacy());
            $this->assertEquals($payload, $article->getPayload());
            $this->assertFileExists($tempRepoDirectory.
                str_replace('/', DIRECTORY_SEPARATOR, $article->getRepositoryPath()
                    ->withExtension(getenv('OBJECT_RESOURCE_EXTENSION'))));
            $this->assertEquals($creationDate, $article->getCreated());

            // Alter and persist the object
            $article->setPayload('Revision 1 draft (updated)');
            $article->persist();

            // Publish and persist the first object revision
            $article->setPayload('Revision 1');
            $article->publish();
            $article->persist();

            // Draft a second object revision
            $article->setPayload('Revision 2 draft');
            $article->persist();

            // Publish and persist the second object revision
            $article->publish();
            $article->setPayload('Revision 2');
            $article->persist();

            // Modify and persist a third object draft revision
            $article->setPayload('Revision 3 draft');
            $article->persist();

            // Wait for 2 seconds, modify and re-persist the object
            $now = time();
            sleep(2);
            $article->setPayload('Revision 3 draft (delayed modification)');
            $article->persist();
            $this->assertGreaterThanOrEqual($now + 2, $article->getModified()->format('U'));

            // Iterate through all object revisions
            foreach ($article as $articleRevisionIndex => $articleRevision) {
                $this->assertInstanceOf(Article::class, $articleRevision);
                $this->assertInstanceOf(Revision::class, $articleRevisionIndex);
            }

            // Publish and persist a third object draft revision
            $article->publish()->persist();

            // Delete the object (and all it's revisions)
            $article->delete()->persist();

            // Undelete the object (and all it's revisions)
            $article->undelete()->persist();

            // Use the first revision
            $article->rewind();

            // Delete temporary repository
            $this->deleteRecursive($tempRepoDirectory);

            $article->persist();
        }

        /**
         * Create a temporary repository and article object
         *
         * @param string $tempRepoDirectory Repository directory
         * @param string $payload Article payload
         * @param \DateTimeInterface $creationDate Article creation date
         * @return Article Article object
         */
        protected function createRepositoryAndArticleObject(
            $tempRepoDirectory,
            $payload,
            \DateTimeInterface $creationDate = null
        ) {
            $fileRepository = \Apparat\Object\Infrastructure\Repository\Repository::create(
                getenv('REPOSITORY_URL'),
                [
                    'type' => FileAdapterStrategy::TYPE,
                    'root' => $tempRepoDirectory,
                ]
            );
            $this->assertInstanceOf(Repository::class, $fileRepository);
            $this->assertEquals($fileRepository->getAdapterStrategy()->getRepositorySize(), 0);

            // Create a new article in the temporary repository
            return $fileRepository->createObject(ObjectTypes::ARTICLE, $payload, [], $creationDate);
        }

        /**
         * Recursively register a directory and all nested files and directories for deletion on teardown
         *
         * @param string $directory Directory
         */
        protected function deleteRecursive($directory)
        {
            $this->tmpFiles[] = $directory;
            foreach (scandir($directory) as $item) {
                if (!preg_match('%^\.+$%', $item)) {
                    $path = $directory.DIRECTORY_SEPARATOR.$item;
                    if (is_dir($path)) {
                        $this->deleteRecursive($path);
                        continue;
                    }

                    $this->tmpFiles[] = $path;
                }
            }
        }

        /**
         * Test the creation and persisting of an article object with failing file lock
         *
         * @expectedException \Apparat\Object\Infrastructure\Repository\RuntimeException
         * @expectedExceptionCode 1464269155
         */
        public function testDeleteArticleObjectImpossible()
        {
            putenv('MOCK_RENAME=1');
            $this->tmpFiles[] = $tempRepoDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo';
            $article = $this->createRepositoryAndArticleObject($tempRepoDirectory, 'Revision 1 draft');
            $this->deleteRecursive($tempRepoDirectory);
            $article->delete()->persist();
        }

        /**
         * Test the creation and persisting of an article object with failing file lock
         *
         * @expectedException \Apparat\Object\Infrastructure\Repository\RuntimeException
         * @expectedExceptionCode 1464269179
         */
        public function testUndeleteArticleObjectImpossible()
        {
            $this->tmpFiles[] = $tempRepoDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo';
            $article = $this->createRepositoryAndArticleObject($tempRepoDirectory, 'Revision 1 draft');
            $article->getRepositoryPath()->getRepository()->deleteObject($article);
            $this->deleteRecursive($tempRepoDirectory);
            putenv('MOCK_RENAME=1');
            $article->undelete()->persist();
        }
    }
}

namespace Apparat\Object\Infrastructure\Repository {

    /**
     * Mocked version of the native flock() function
     *
     * @param resource $handle An open file pointer.
     * @param int $operation Operation is one of the following: LOCK_SH to acquire a shared lock (reader).
     * @param int $wouldblock The optional third argument is set to true if the lock would block (EWOULDBLOCK errno
     *     condition).
     * @return bool True on success or False on failure.
     */
    function flock($handle, $operation, &$wouldblock = null)
    {
        return (getenv('MOCK_FLOCK') != 1) ? \flock($handle, $operation, $wouldblock) : false;
    }

    /**
     * Mocked version of the native rename() function
     *
     * @param string $oldname The old name. The wrapper used in oldname must match the wrapper used in newname.
     * @param @param string $newname The new name.
     * @return bool true on success or false on failure.
     */
    function rename($oldname, $newname)
    {
        return (getenv('MOCK_RENAME') != 1) ? \rename($oldname, $newname) : false;
    }
}
