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
use Apparat\Object\Infrastructure\Model\Object\Apparat\Article;
use Apparat\Object\Infrastructure\Model\Object\Object;

/**
 * Object URL tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Test
 */
class ApparatObjectTest extends AbstractRepositoryEnabledTest
{
    /**
     * Example object path
     *
     * @var string
     */
    const ARTICLE_PATH = '/repo/2015/12/21/1-article/1';

    /**
     * Test the article apparat object
     *
     * @expectedException \BadMethodCallException
     */
    public function testArticleApparatObjectInvalidGetter()
    {
        /** @var ApplicationArticle $articleObj */
        $articleObj = Object::load(self::ARTICLE_PATH);
        $articleApparatObj = Kernel::create(Article::class, [$articleObj]);
        $this->assertInstanceOf(Article::class, $articleApparatObj);

        $this->assertTrue(isset($articleApparatObj['name']));
        $this->assertEquals('First repository article', $articleApparatObj['name']);
        $this->assertEquals('First repository article', $articleApparatObj->getName());
        $articleApparatObj['name'] = null;
        $articleApparatObj->getInvalid();
    }

    /**
     * Test illegal unsetting of apparat object property
     *
     * @expectedException \Apparat\Object\Ports\Exceptions\InvalidArgumentException
     * @expectedExceptionCode 1465330565
     */
    public function testArticleApparatObjectUnvalidUnset()
    {
        /** @var ApplicationArticle $articleObj */
        $articleObj = Object::load(self::ARTICLE_PATH);
        $articleApparatObj = Kernel::create(Article::class, [$articleObj]);
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
        $articleObj = Object::load(self::ARTICLE_PATH);
        $articleApparatObj = Kernel::create(TestApparatObject::class, [$articleObj]);
        $articleApparatObj['invalid'];
    }
}
