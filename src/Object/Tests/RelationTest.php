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

use Apparat\Object\Application\Model\Object\Article;
use Apparat\Object\Domain\Factory\RelationFactory;
use Apparat\Object\Domain\Repository\Repository;
use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;
use Apparat\Object\Ports\Object;
use Apparat\Object\Ports\Relation;

/**
 * Object relation test
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class RelationTest extends AbstractDisabledAutoconnectorTest
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
     * Test the addition of an object relation
     *
     * @expectedException \Apparat\Object\Domain\Model\Relation\OutOfBoundsException
     * @expectedExceptionCode 1462401333
     */
    public function testObjectAddRelation()
    {
        $article = Object::instance(getenv('REPOSITORY_URL').self::OBJECT_PATH);
        $this->assertInstanceOf(Article::class, $article);
        $article->addRelation('http://example.com <john@example.com> John Doe', Relation::EMBEDDED_BY);
        $this->assertEquals(2, count($article->findRelations([Relation::URL => 'example.com'])));
        foreach ($article->findRelations([Relation::EMAIL => 'tollwerk.de']) as $relation) {
            $article->deleteRelation($relation);
        }
        $this->assertEquals(2, count($article->getRelations()));
        $article->addRelation('http://example.com <john@example.com> John Doe', 'invalid');
    }

    /**
     * Test a relation construction with repeated email
     *
     * @expectedException \Apparat\Object\Domain\Model\Relation\InvalidArgumentException
     * @expectedExceptionCode 1462395977
     */
    public function testInvalidRelationEmail()
    {
        RelationFactory::createFromString(Relation::CONTRIBUTED_BY, '<invalid', self::$repository );
    }

    /**
     * Test a relation construction with repeated email
     *
     * @expectedException \Apparat\Object\Domain\Model\Relation\InvalidArgumentException
     * @expectedExceptionCode 1462394737
     */
    public function testRepeatedRelationEmail()
    {
        RelationFactory::createFromString(
            Relation::CONTRIBUTED_BY,
            '<test@example.com> <test@example.com>',
            self::$repository
        );
    }

    /**
     * Test a relation construction with repeated URL
     *
     * @expectedException \Apparat\Object\Domain\Model\Relation\InvalidArgumentException
     * @expectedExceptionCode 1462394737
     */
    public function testRepeatedRelationUrl()
    {
        RelationFactory::createFromString(
            Relation::CONTRIBUTED_BY,
            'http://example.com http://example.com',
            self::$repository
        );
    }
}
