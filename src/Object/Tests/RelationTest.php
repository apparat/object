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

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Application\Model\Object\Article;
use Apparat\Object\Domain\Factory\RelationFactory;
use Apparat\Object\Domain\Model\Path\Url;
use Apparat\Object\Domain\Model\Properties\Relations;
use Apparat\Object\Domain\Model\Relation\ContributedByRelation;
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
     * Test a relation deserialization with repeated email
     *
     * @expectedException \Apparat\Object\Domain\Model\Relation\InvalidArgumentException
     * @expectedExceptionCode 1462395977
     */
    public function testInvalidRelationEmail()
    {
        RelationFactory::createFromString(Relation::CONTRIBUTED_BY, '<invalid', self::$repository);
    }

    /**
     * Test a relation deserialization with repeated email
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
     * Test a relation deserialization with repeated URL
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

    /**
     * Test a relation construction
     *
     * @expectedException \Apparat\Object\Domain\Model\Properties\InvalidArgumentException
     * @expectedExceptionCode 1462703468
     */
    public function testRelationConstruction()
    {
        $article = Object::instance(getenv('REPOSITORY_URL').self::OBJECT_PATH);

        /** @var Relations $relations */
        $relations = Kernel::create(Relations::class, [[Relation::CONTRIBUTED_BY => []], $article]);

        // Multiple relation addition
        $relations = $relations->addRelation('http://example.org John Doe', Relation::CONTRIBUTED_BY);
        $relations = $relations->addRelation('http://example.org John Doe', Relation::CONTRIBUTED_BY);

        // Retrieve contributed-by relations
        $contributedByRels = $relations->getRelations(Relation::CONTRIBUTED_BY);
        $this->assertEquals(1, count($contributedByRels));

        // Multiple relation deletion
        $relations = $relations->deleteRelation($contributedByRels[0]);
        $relations = $relations->deleteRelation($contributedByRels[0]);

        // Add invalid relation
        $relations->addRelation(new \stdClass());
    }

    /**
     * Test the filtering of relations
     */
    public function testRelationFiltering()
    {
        $article = Object::instance(getenv('REPOSITORY_URL').self::OBJECT_PATH);

        /** @var Relations $relations */
        $relations = Kernel::create(Relations::class, [[Relation::CONTRIBUTED_BY => []], $article]);
        $relations = $relations->addRelation(
            '!/repo/2016/01/08/2.contact/2 <john@example.com> John Doe',
            Relation::CONTRIBUTED_BY
        );

        // Filter by type
        $this->assertEquals(1, count($relations->findRelations([Relation::TYPE => Relation::CONTRIBUTED_BY])));
        $this->assertEquals(0, count($relations->findRelations([Relation::TYPE => Relation::CONTRIBUTES])));

        // Filter by URL
        $this->assertEquals(1, count($relations->findRelations([Relation::URL => 'repo'])));
        $this->assertEquals(0, count($relations->findRelations([Relation::URL => 'example.com'])));

        // Filter by email
        $this->assertEquals(1, count($relations->findRelations([Relation::EMAIL => '@example.com'])));
        $this->assertEquals(0, count($relations->findRelations([Relation::EMAIL => '@test.com'])));

        // Filter by label
        $this->assertEquals(1, count($relations->findRelations([Relation::LABEL => 'John'])));
        $this->assertEquals(0, count($relations->findRelations([Relation::LABEL => 'Jane'])));

        // Filter by coupling
        $this->assertEquals(1, count($relations->findRelations([Relation::COUPLING => true])));
        $this->assertEquals(0, count($relations->findRelations([Relation::COUPLING => false])));

        // Filter by invalid criteria
        $this->assertEquals(0, count($relations->findRelations(['invalid' => 'invalid'])));
    }

    /**
     * Test invalid relation coupling
     *
     * @expectedException \Apparat\Object\Domain\Model\Relation\OutOfBoundsException
     * @expectedExceptionCode 1462311299
     */
    public function testInvalidRelationCoupling()
    {
        Kernel::create(ContributedByRelation::class, ['Label', 'john@example.com', 'invalid-coupling']);
    }

    /**
     * Test relation getters & setters
     *
     * @expectedException \Apparat\Object\Domain\Model\Relation\OutOfBoundsException
     * @expectedExceptionCode 1462311299
     */
    public function testRelationGetterSetters()
    {
        $url = Kernel::create(Url::class, [self::OBJECT_PATH]);
        $this->assertInstanceOf(Url::class, $url);

        /** @var ContributedByRelation $relation */
        $relation = Kernel::create(
            ContributedByRelation::class,
            [$url, 'label', 'john@example.com', Relation::LOOSE_COUPLING]
        );
        $this->assertInstanceOf(ContributedByRelation::class, $relation);

        // Set the URL
        $url2 = Kernel::create(Url::class, ['http://example.com/test']);
        $this->assertInstanceOf(Url::class, $url2);
        $relation = $relation->setUrl($url2)
            ->setLabel('Modified label')
            ->setEmail('jane@example.com')
            ->setCoupling(Relation::TIGHT_COUPLING);
        $relation->setCoupling('invalid-coupling');
    }
}
