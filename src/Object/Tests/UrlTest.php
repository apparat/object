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

use Apparat\Object\Domain\Model\Path\ApparatUrl;
use Apparat\Object\Domain\Model\Path\LocalPath;
use Apparat\Object\Domain\Model\Path\Url;
use Apparat\Object\Domain\Repository\Service;
use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;
use Apparat\Object\Ports\Repository;

/**
 * Object URL tests
 *
 * @package Apparat\Object
 * @subpackage ApparatTest
 */
class UrlTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Example query fragment
     *
     * @var string
     */
    const QUERY_FRAGMENT = '?param=value#fragment';
    /**
     * Repository URL
     *
     * @var string
     */
    const REPOSITORY_URL = '/repo';
    /**
     * Example path
     *
     * @var string
     */
    const PATH = '/2015/10/01/36704-event/36704-1';
    /**
     * Example path (draft mode)
     *
     * @var string
     */
    const DRAFT_PATH = '/2015/10/01/36704-event/36704+';
    /**
     * Example URL
     *
     * @var string
     */
    const URL = self::REPOSITORY_URL.self::PATH.self::QUERY_FRAGMENT;
    /**
     * Example remote repository authority
     *
     * @var string
     */
    const REMOTE_REPOSITORY_AUTHORITY = 'apparat:tools@apparat.tools:80';
    /**
     * Example remote repository URL
     *
     * @var string
     */
    const REMOTE_REPOSITORY_URL = 'http://'.self::REMOTE_REPOSITORY_AUTHORITY;
    /**
     * Example remote URL
     *
     * @var string
     */
    const REMOTE_URL = self::REMOTE_REPOSITORY_URL.self::PATH.self::QUERY_FRAGMENT;
    /**
     * Example apparat URL
     *
     * @var string
     */
    const APPARAT_URL = 'aprts://apparat:tools@apparat.tools:80'.self::PATH.self::QUERY_FRAGMENT;

    /**
     * Test URL comparison
     */
    public function testUrlComparison()
    {
        $this->assertFalse((new Url('http://example.com'))->matches(new Url('https://example.com')));
        $this->assertFalse((new Url('http://user1@example.com'))->matches(new Url('http://user2@example.com')));
        $this->assertFalse((new Url('http://user:pass1@example.com'))->matches(
            new Url('http://user:pass2@example.com')
        ));
        $this->assertFalse((new Url('http://example1.com'))->matches(new Url('http://example2.com')));
        $this->assertFalse((new Url('http://example.com:80'))->matches(new Url('http://example.com:443')));
        $this->assertFalse((new Url('http://example.com/a'))->matches(new Url('http://example.com/b')));
        $this->assertFalse((new Url('http://example.com/?a=1'))->matches(new Url('http://example.com/?a=2')));
        $this->assertFalse((new Url('http://example.com/#a'))->matches(new Url('http://example.com/#b')));
        $this->assertTrue((new Url(self::REMOTE_URL))->matches(new Url(self::REMOTE_URL)));
    }

    /**
     * Test an invalid apparat URL
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\InvalidArgumentException
     * @expectedExceptionCode 1451435429
     */
    public function testInvalidApparatUrl()
    {
        new ApparatUrl(self::REMOTE_URL, true);
    }

    /**
     * Test an absolute apparat URL
     */
    public function testAbsoluteApparatUrl()
    {
        $apparatUrl = new ApparatUrl(self::APPARAT_URL, true);
        $this->assertInstanceOf(ApparatUrl::class, $apparatUrl);
        $this->assertEquals('https://apparat:tools@apparat.tools:80', Service::normalizeRepositoryUrl($apparatUrl));
    }

    /**
     * Test an unknown relative apparat URL
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\ApparatInvalidArgumentException
     * @expectedExceptionCode 1452695654
     */
    public function testUnknownRelativeApparatUrl()
    {
        new ApparatUrl(self::PATH.self::QUERY_FRAGMENT);
    }

    /**
     * Test a relative apparat URL
     */
    public function testRelativeApparatUrl()
    {
        Repository::register(
            self::REPOSITORY_URL,
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => __DIR__,
            ]
        );
        $apparatUrl = new ApparatUrl(self::URL);
        $this->assertInstanceOf(ApparatUrl::class, $apparatUrl);
        $this->assertEquals(self::REPOSITORY_URL, Service::normalizeRepositoryUrl($apparatUrl));
    }

    /**
     * Test invalid date precision
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\InvalidArgumentException
     * @expectedExceptionCode 1451514114
     */
    public function testInvalidDatePrecision()
    {
        new LocalPath(self::PATH, -1);
    }

    /**
     * Test arbitrary date precision
     */
    public function testArbitraryDatePrecision()
    {
        $path = new LocalPath(self::PATH, true);
        $this->assertInstanceOf(LocalPath::class, $path);
    }

    /**
     * Test draft path
     */
    public function testDraftPath()
    {
        $path = new LocalPath(self::DRAFT_PATH);
        $this->assertInstanceOf(LocalPath::class, $path);
        $this->assertTrue($path->getRevision()->isDraft());
    }

    /**
     * Test the normalization of an invalid repository URL
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1453097878
     */
    public function testInvalidRepositoryUrlNormalization()
    {
        Service::normalizeRepositoryUrl(new Url(self::REMOTE_REPOSITORY_URL));
    }

    /**
     * Test the normalization of a local string repository URL
     */
    public function testLocalStringUrlNormalization()
    {
        $this->assertEquals(
            self::REPOSITORY_URL.self::PATH,
            Service::normalizeRepositoryUrl(getenv('APPARAT_BASE_URL').self::REPOSITORY_URL.self::PATH)
        );
    }

    /**
     * Test the remaining PSR-7 methods
     */
    public function testPSR7methods() {
        $url = new Url(self::REMOTE_URL);
        $this->assertEquals(self::REMOTE_REPOSITORY_AUTHORITY, $url->getAuthority());
        $this->assertEquals('apparat:tools', $url->getUserInfo());
        $this->assertEquals('https://'.self::REMOTE_REPOSITORY_AUTHORITY.self::PATH.self::QUERY_FRAGMENT, strval($url->withScheme('HTTPS')));
        $this->assertEquals('http://test@apparat.tools:80'.self::PATH.self::QUERY_FRAGMENT, strval($url->withUserInfo('test')));
        $this->assertEquals('http://apparat:tools@test.com:80'.self::PATH.self::QUERY_FRAGMENT, strval($url->withHost('test.com')));
        $this->assertEquals('http://apparat:tools@apparat.tools:443'.self::PATH.self::QUERY_FRAGMENT, strval($url->withPort(443)));
        $this->assertEquals('http://apparat:tools@apparat.tools'.self::PATH.self::QUERY_FRAGMENT, strval($url->withPort(null)));
        $this->assertEquals('http://apparat:tools@apparat.tools:80/test/path'.self::QUERY_FRAGMENT, strval($url->withPath('test/path')));
        $this->assertEquals('http://apparat:tools@apparat.tools:80'.self::PATH.'?param2=value2#fragment', strval($url->withQuery('param2=value2')));
        $this->assertEquals('http://apparat:tools@apparat.tools:80'.self::PATH.'?param=value#fragment2', strval($url->withFragment('fragment2')));
    }
}
