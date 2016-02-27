<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Tests
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

use Apparat\Object\Domain\Factory\AuthorFactory;
use Apparat\Object\Domain\Model\Author\ApparatAuthor;
use Apparat\Object\Domain\Model\Author\GenericAuthor;
use Apparat\Object\Domain\Model\Author\InvalidAuthor;
use Apparat\Object\Domain\Model\Path\ApparatInvalidArgumentException;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Repository\Repository;
use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;

/**
 * Author tests
 *
 * @package Apparat\Kernel
 * @subpackage Apparat\Object\Tests
 */
class AuthorTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Test repository
     *
     * @var Repository
     */
    protected static $repository = null;

    /**
     * Generic author string
     *
     * @var string
     */
    const GENERIC_AUTHOR = 'John Doe <john@doe.com> (http://doe.com)';

    /**
     * Setup
     */
    public static function setUpBeforeClass()
    {
        \Apparat\Object\Ports\Repository::register(
            getenv('REPOSITORY_URL'), [
                'type' => FileAdapterStrategy::TYPE,
                'root' => __DIR__ . DIRECTORY_SEPARATOR . 'Fixture',
            ]
        );

        self::$repository = \Apparat\Object\Ports\Repository::instance(getenv('REPOSITORY_URL'));
    }

    /**
     * Load an article and test an invalid author
     *
     * @expectedException \Apparat\Object\Domain\Model\Properties\InvalidArgumentException
     * @expectedExceptionCode 1451425516
     */
    public function testLoadArticleObjectInvalidAuthor()
    {
        $articleObjectPath = new RepositoryPath(self::$repository, '/2016/02/16/4.article/4');
        self::$repository->loadObject($articleObjectPath);
    }

    /**
     * Test an invalid author format
     *
     * @expectedException \Apparat\Object\Domain\Model\Author\InvalidArgumentException
     * @expectedExceptionCode 1451426440
     */
    public function testInvalidAuthorFormat()
    {
        AuthorFactory::createFromString('');
    }

    /**
     * Test apparat author unserialization
     */
    public function testApparatAuthorUnserialization() {
        $this->assertInstanceOf(ApparatAuthor::class, ApparatAuthor::unserialize('/repo/2016/01/08/2.contact/2'));
    }

    /**
     * Test the serialization of a generic author
     */
    public function testGenericAuthorSerialization() {
        $genericAuthor = GenericAuthor::unserialize(self::GENERIC_AUTHOR);
        $this->assertEquals(self::GENERIC_AUTHOR, $genericAuthor->serialize());
    }

    /**
     * Test invalid author
     */
    public function testInvalidAuthor() {
        /** @var InvalidAuthor $invalidAuthor */
        $invalidAuthor = AuthorFactory::createFromString('ftp://apparat.tools/2015/10/01/36704.event/36704-1');
        $this->assertInstanceOf(InvalidAuthor::class, $invalidAuthor);
        $exception = $invalidAuthor->getException();
        $this->assertInstanceOf(ApparatInvalidArgumentException::class, $exception);
        $this->assertEquals(ApparatInvalidArgumentException::INVALID_ABSOLUTE_APPARAT_URL, $exception->getCode());
    }

    /**
     * Test invalid author unserialization
     */
    public function testInvalidAuthorUnserialization() {
        /** @var InvalidAuthor $invalidAuthor */
        $invalidAuthor = InvalidAuthor::unserialize('ftp://apparat.tools/2015/10/01/36704.event/36704-1');
        $this->assertInstanceOf(InvalidAuthor::class, $invalidAuthor);
        $this->assertEquals(null, $invalidAuthor->getException());
    }
}
