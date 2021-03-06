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
        $this->assertEquals($data[5], $selector->getObjectType());
        $this->assertEquals($data[6], $selector->getDraft());
        $this->assertEquals($data[7], $selector->getRevision());
    }

    /**
     * Provide selectors
     */
    public function getSelector()
    {
        $wlc = SelectorInterface::WILDCARD;
        $vsb = SelectorInterface::VISIBLE;
        $pub = SelectorInterface::PUBLISHED;
        $all = SelectorInterface::ALL;
        $hid = SelectorInterface::HIDDEN;
        $dft = SelectorInterface::DRAFT;
        $cur = Revision::CURRENT;
        return [
            ['/2016', ['2016', $wlc, $wlc, $vsb, $wlc, $wlc, $pub, $cur]],
            ['/*', [$wlc, $wlc, $wlc, $vsb, $wlc, $wlc, $pub, $cur]],
            ['/2016/06', ['2016', '06', $wlc, $vsb, $wlc, $wlc, $pub, $cur]],
            ['/2016/*', ['2016', $wlc, $wlc, $vsb, $wlc, $wlc, $pub, $cur]],
            ['/2016/06/16', ['2016', '06', '16', $vsb, $wlc, $wlc, $pub, $cur]],
            ['/2016/06/*', ['2016', '06', $wlc, $vsb, $wlc, $wlc, $pub, $cur]],
            ['/2016/06/16/123', ['2016', '06', '16', $vsb, 123, $wlc, $pub, $cur]],
            ['/2016/06/16/.123', ['2016', '06', '16', $hid, 123, $wlc, $pub, $cur]],
            ['/2016/06/16/~123', ['2016', '06', '16', $all, 123, $wlc, $pub, $cur]],
            ['/2016/06/16/*', ['2016', '06', '16', $vsb, $wlc, $wlc, $pub, $cur]],
            ['/2016/06/16/.*', ['2016', '06', '16', $hid, $wlc, $wlc, $pub, $cur]],
            ['/2016/06/16/~*', ['2016', '06', '16', $all, $wlc, $wlc, $pub, $cur]],
            ['/2016/06/16/123/123', ['2016', '06', '16', $vsb, 123, $wlc, $pub, $cur]],
            ['/2016/06/16/123-article', ['2016', '06', '16', $vsb, 123, 'article', $pub, $cur]],
            ['/2016/06/16/123-*', ['2016', '06', '16', $vsb, 123, $wlc, $pub, $cur]],
            ['/2016/06/16/123-article/123', ['2016', '06', '16', $vsb, 123, 'article', $pub, $cur]],
            ['/2016/06/16/123-article/*', ['2016', '06', '16', $vsb, 123, 'article', $pub, $cur]],
            ['/2016/06/16/123-article/.123', ['2016', '06', '16', $vsb, 123, 'article', $dft, $cur]],
            ['/2016/06/16/123-article/~123', ['2016', '06', '16', $vsb, 123, 'article', $all, $cur]],
            ['/2016/06/16/123-article/.*', ['2016', '06', '16', $vsb, 123, 'article', $dft, $cur]],
            ['/2016/06/16/123-article/~*', ['2016', '06', '16', $vsb, 123, 'article', $all, $cur]],
            ['/2016/06/16/123-article/123-99', ['2016', '06', '16', $vsb, 123, 'article', $pub, 99]],
            ['/2016/06/16/123-article/123-*', ['2016', '06', '16', $vsb, 123, 'article', $pub, $wlc]],
            ['/2016/06/16/123-article/.123-99', ['2016', '06', '16', $vsb, 123, 'article', $hid, 99]],
            ['/2016/06/16/123-article/.123-*', ['2016', '06', '16', $vsb, 123, 'article', $hid, $wlc]],
            ['/2016/06/16/123-article/~123-99', ['2016', '06', '16', $vsb, 123, 'article', $all, 99]],
            ['/2016/06/16/123-article/~123-*', ['2016', '06', '16', $vsb, 123, 'article', $all, $wlc]],
            ['/*/06/16/123-article/123-99', [$wlc, '06', '16', $vsb, 123, 'article', $pub, 99]],
            ['/2016/*/16/123-article/123-99', ['2016', $wlc, '16', $vsb, 123, 'article', $pub, 99]],
            ['/2016/06/*/123-article/123-99', ['2016', '06', $wlc, $vsb, 123, 'article', $pub, 99]],
            ['/2016/06/16/*-article/*-99', ['2016', '06', '16', $vsb, $wlc, 'article', $pub, 99]],
            ['/2016/06/16/123-*/123-99', ['2016', '06', '16', $vsb, 123, $wlc, $pub, 99]],
            ['/2016/06/16/123-article/*-99', ['2016', '06', '16', $vsb, 123, 'article', $pub, 99]],
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
