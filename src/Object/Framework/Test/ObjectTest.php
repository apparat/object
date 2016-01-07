<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framework
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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

namespace ApparatTest;

use Apparat\Object\Application\Factory\ObjectFactory;
use Apparat\Object\Application\Model\Object\Article;
use Apparat\Object\Domain\Model\Object\ResourceInterface;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Model\Properties\SystemProperties;
use Apparat\Object\Domain\Repository\Repository;
use Apparat\Object\Framework\Api\Object;
use Apparat\Object\Framework\Repository\FileAdapterStrategy;

/**
 * Object tests
 *
 * @package Apparat\Object
 * @subpackage ApparatTest
 */
class ObjectTest extends AbstractTest
{
	/**
	 * Test repository
	 *
	 * @var Repository
	 */
	protected static $_repository = null;

	/**
	 * Example object path
	 *
	 * @var string
	 */
	const OBJECT_PATH = '/2015/12/21/1.article/1';

	/**
	 * Setup
	 */
	public static function setUpBeforeClass()
	{
		\Apparat\Object\Framework\Api\Repository::register(getenv('REPOSITORY_URL'), [
			'type' => FileAdapterStrategy::TYPE,
			'root' => __DIR__.DIRECTORY_SEPARATOR.'Fixture',
		]);

		self::$_repository = \Apparat\Object\Framework\Api\Repository::instance(getenv('REPOSITORY_URL'));
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
	 * @expectedException \Apparat\Object\Application\Factory\InvalidArgumentException
	 * @expectedExceptionCode 1450824842
	 */
	public function testInvalidObjectType()
	{
		$resource = $this->getMock(ResourceInterface::class);
		$resource->method('getPropertyData')->willReturn([SystemProperties::COLLECTION => ['type' => 'invalid']]);
		$repositoryPath = $this->getMockBuilder(RepositoryPath::class)->disableOriginalConstructor()->getMock();

		/** @var ResourceInterface $resource */
		/** @var RepositoryPath $repositoryPath */
		ObjectFactory::createFromResource($resource, $repositoryPath);
	}

	/**
	 * Load an article object and test its meta properties
	 */
	public function testLoadArticleObjectMetaProperties()
	{
		$articleObjectPath = new RepositoryPath(self::$_repository, self::OBJECT_PATH);
		$articleObject = self::$_repository->loadObject($articleObjectPath);
		$this->assertInstanceOf(Article::class, $articleObject);
		$this->assertArrayEquals(['apparat', 'object', 'example', 'article'], $articleObject->getKeywords());
		$this->assertArrayEquals(['example', 'text'], $articleObject->getCategories());
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
	}

	/**
	 * Test the object facade with an invalid relative object URL
	 *
	 * @expectedException \Apparat\Resource\Framework\Io\File\InvalidArgumentException
	 * @expectedExceptionCode 1447616824
	 */
	public function testObjectFacadeRelativeInvalid()
	{
		$object = Object::instance(getenv('REPOSITORY_URL').'/2015/12/21/2.article/2');
		$this->assertInstanceOf(Article::class, $object);
	}
}