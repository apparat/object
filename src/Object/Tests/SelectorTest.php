<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framwork
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
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Repository\Selector;
use Apparat\Object\Domain\Repository\Selector as RepositorySelector;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Object\Ports\Factory\SelectorFactory;
use Apparat\Object\Ports\Types\Object as ObjectTypes;

/**
 * Selector tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Test
 */
class SelectorTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Test repository selectors
     *
     * @dataProvider getSelector
     * @param string $selector Selector
     * @param array $data Data
     */
    public function testFactorySelectors($selector, array $data)
    {
        $selector = SelectorFactory::createFromString($selector);
        $this->assertInstanceOf(Selector::class, $selector);
        $this->assertEquals($data[0], $selector->getYear());
        $this->assertEquals($data[1], $selector->getMonth());
        $this->assertEquals($data[2], $selector->getDay());
        $this->assertEquals($data[3], $selector->getVisibility());
        $this->assertEquals($data[4], $selector->getId());
        $this->assertEquals($data[5], $selector->getType());
        $this->assertEquals($data[6], $selector->getDraft());
        $this->assertEquals($data[7], $selector->getRevision());
    }

    /**
     * Provide selectors
     */
    public function getSelector()
    {
        $w = SelectorInterface::WILDCARD;
        $v = SelectorInterface::VISIBLE;
        $p = SelectorInterface::PUBLISHED;
        $a = SelectorInterface::ALL;
        $h = SelectorInterface::HIDDEN;
        $d = SelectorInterface::DRAFT;
        $c = Revision::CURRENT;
        return [
            ['/2016', ['2016', $w, $w, $v, $w, $w, $p, $c]],
            ['/*', [$w, $w, $w, $v, $w, $w, $p, $c]],
            ['/2016/06', ['2016', '06', $w, $v, $w, $w, $p, $c]],
            ['/2016/*', ['2016', $w, $w, $v, $w, $w, $p, $c]],
            ['/2016/06/16', ['2016', '06', '16', $v, $w, $w, $p, $c]],
            ['/2016/06/*', ['2016', '06', $w, $v, $w, $w, $p, $c]],
            ['/2016/06/16/123', ['2016', '06', '16', $v, 123, $w, $p, $c]],
            ['/2016/06/16/.123', ['2016', '06', '16', $h, 123, $w, $p, $c]],
            ['/2016/06/16/~123', ['2016', '06', '16', $a, 123, $w, $p, $c]],
            ['/2016/06/16/*', ['2016', '06', '16', $v, $w, $w, $p, $c]],
            ['/2016/06/16/.*', ['2016', '06', '16', $h, $w, $w, $p, $c]],
            ['/2016/06/16/~*', ['2016', '06', '16', $a, $w, $w, $p, $c]],
            ['/2016/06/16/123/123', ['2016', '06', '16', $v, 123, $w, $p, $c]],
            ['/2016/06/16/123-article', ['2016', '06', '16', $v, 123, 'article', $p, $c]],
            ['/2016/06/16/123-*', ['2016', '06', '16', $v, 123, $w, $p, $c]],
            ['/2016/06/16/123-article/123', ['2016', '06', '16', $v, 123, 'article', $p, $c]],
            ['/2016/06/16/123-article/*', ['2016', '06', '16', $v, 123, 'article', $p, $c]],
            ['/2016/06/16/123-article/.123', ['2016', '06', '16', $v, 123, 'article', $d, $c]],
            ['/2016/06/16/123-article/~123', ['2016', '06', '16', $v, 123, 'article', $a, $c]],
            ['/2016/06/16/123-article/.*', ['2016', '06', '16', $v, 123, 'article', $d, $c]],
            ['/2016/06/16/123-article/~*', ['2016', '06', '16', $v, 123, 'article', $a, $c]],
            ['/2016/06/16/123-article/123-99', ['2016', '06', '16', $v, 123, 'article', $p, 99]],
            ['/2016/06/16/123-article/123-*', ['2016', '06', '16', $v, 123, 'article', $p, $w]],
            ['/2016/06/16/123-article/.123-99', ['2016', '06', '16', $v, 123, 'article', $h, 99]],
            ['/2016/06/16/123-article/.123-*', ['2016', '06', '16', $v, 123, 'article', $h, $w]],
            ['/2016/06/16/123-article/~123-99', ['2016', '06', '16', $v, 123, 'article', $a, 99]],
            ['/2016/06/16/123-article/~123-*', ['2016', '06', '16', $v, 123, 'article', $a, $w]],
            ['/*/06/16/123-article/123-99', [$w, '06', '16', $v, 123, 'article', $p, 99]],
            ['/2016/*/16/123-article/123-99', ['2016', $w, '16', $v, 123, 'article', $p, 99]],
            ['/2016/06/*/123-article/123-99', ['2016', '06', $w, $v, 123, 'article', $p, 99]],
            ['/2016/06/16/*-article/*-99', ['2016', '06', '16', $v, $w, 'article', $p, 99]],
            ['/2016/06/16/123-*/123-99', ['2016', '06', '16', $v, 123, $w, $p, 99]],
            ['/2016/06/16/123-article/*-99', ['2016', '06', '16', $v, 123, 'article', $p, 99]],
        ];
    }

    /**
     * Test an invalid selector
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449961609
     */
    public function testFactoryInvalidSelector()
    {
        SelectorFactory::createFromString('invalid');
    }

    /**
     * Test an invalid date component
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %year%
     */
    public function testInvalidDateComponent()
    {
        Kernel::create(RepositorySelector::class, ['invalid']);
    }

    /**
     * Test an invalid ID component
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %id%
     */
    public function testInvalidIdComponent()
    {
        Kernel::create(RepositorySelector::class, [2015, 1, 1, null, null, null, 'invalid']);
    }

    /**
     * Test an invalid type component
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %type%
     */
    public function testInvalidTypeComponent()
    {
        Kernel::create(
            RepositorySelector::class,
            [2015, 1, 1, null, null, null, 1, 'invalid', SelectorInterface::PUBLISHED]
        );
    }

    /**
     * Test an invalid revision component
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %revision%
     */
    public function testInvalidRevisionComponent()
    {
        Kernel::create(
            RepositorySelector::class,
            [2015, 1, 1, null, null, null, 1, ObjectTypes::ARTICLE, 'invalid', SelectorInterface::VISIBLE,
                SelectorInterface::PUBLISHED]
        );
    }

    /**
     * Test an invalid object visibility
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %visibility%
     */
    public function testInvalidVisibilityComponent()
    {
        Kernel::create(
            RepositorySelector::class,
            [2015, 1, 1, null, null, null, 1, ObjectTypes::ARTICLE, Revision::CURRENT, 0, SelectorInterface::PUBLISHED]
        );
    }

    /**
     * Test an invalid object draft state
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449999646
     * @expectedExceptionMessageRegExp %draft%
     */
    public function testInvalidDraftComponent()
    {
        Kernel::create(
            RepositorySelector::class,
            [2015, 1, 1, null, null, null, 1, ObjectTypes::ARTICLE, Revision::CURRENT, SelectorInterface::VISIBLE, 4]
        );
    }
}
