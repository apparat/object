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
use Apparat\Object\Infrastructure\Model\Object\Object;
use Apparat\Object\Ports\Types\Relation;

/**
 * Object payload test
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class PayloadTest extends AbstractRepositoryEnabledTest
{
    /**
     * Example object path
     *
     * @var string
     */
    const OBJECT_PATH = '/2015/12/21/1-article/1';

    /**
     * Test setting the object payload
     */
    public function testObjectSetPayload()
    {
        $article = Object::load(getenv('REPOSITORY_URL').self::OBJECT_PATH);
        $this->assertInstanceOf(Article::class, $article);

        // Set the article payload
        $article->setPayload(file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'non-repo'.
            DIRECTORY_SEPARATOR.'commonmark.md'));
        $this->assertEquals(3, count($article->findRelations([Relation::TYPE => Relation::EMBEDS])));
        $this->assertEquals(15, count($article->findRelations([Relation::TYPE => Relation::REFERS_TO])));
    }
}
