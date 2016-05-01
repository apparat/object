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

use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Path\ApparatUrl;
use Apparat\Object\Domain\Model\Path\LocalPath;
use Apparat\Object\Domain\Model\Path\ObjectUrl;
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
    const PATH = '/2015/10/01/36704.event/36704-1';
    /**
     * Example path (draft mode)
     *
     * @var string
     */
    const DRAFT_PATH = '/2015/10/01/36704.event/36704+';
    /**
     * Example URL
     *
     * @var string
     */
    const URL = self::REPOSITORY_URL.self::PATH.self::QUERY_FRAGMENT;
    /**
     * Example remote repository URL
     *
     * @var string
     */
    const REMOTE_REPOSITORY_URL = 'http://apparat:tools@apparat.tools:80';
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
     * Test an URL
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\InvalidArgumentException
     * @expectedExceptionCode 1451515385
     */
    public function testInvalidRemoteUrl()
    {
        new ObjectUrl(self::REMOTE_URL);
    }

    /**
     * Test a remote URL
     */
    public function testRemoteUrl()
    {
        $url = new ObjectUrl(self::REMOTE_URL, true);
        $this->assertInstanceOf(ObjectUrl::class, $url);
        $this->assertEquals(self::REMOTE_URL, strval($url));
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('apparat', $url->getUser());
        $this->assertEquals('tools', $url->getPassword());
        $this->assertEquals('apparat.tools', $url->getHost());
        $this->assertEquals(80, $url->getPort());
        $this->assertEquals('', $url->getPath());
        $this->assertEquals(['param' => 'value'], $url->getQuery());
        $this->assertEquals('fragment', $url->getFragment());
        $this->assertInstanceOf(\DateTimeImmutable::class, $url->getCreationDate());
        $this->assertEquals('2015-10-01', $url->getCreationDate()->format('Y-m-d'));
        $this->assertInstanceOf(Id::class, $url->getId());
        $this->assertEquals(new Id(36704), $url->getId());
        $this->assertInstanceOf(Type::class, $url->getType());
        $this->assertEquals(new Type('event'), $url->getType());
        $this->assertInstanceOf(Revision::class, $url->getRevision());
        $this->assertEquals(new Revision(1), $url->getRevision());
        $this->assertEquals(self::REMOTE_REPOSITORY_URL, Service::normalizeRepositoryUrl($url));
        $this->assertFalse($url->isDraft());
        $this->assertTrue($url->setDraft(true)->isDraft());
    }

    /**
     * Test a remote draft URL
     */
    public function testRemoteDraftUrl() {
        $url = new ObjectUrl(self::REMOTE_REPOSITORY_URL.self::DRAFT_PATH, true);
        $this->assertInstanceOf(ObjectUrl::class, $url);
        $this->assertTrue($url->isDraft());
    }

    /**
     * Test a local URL with path prefix
     */
    public function testLeadedLocalUrl()
    {
        $pathPrefix = '/prefix/path';
        $url = new ObjectUrl($pathPrefix.self::PATH);
        $this->assertEquals($pathPrefix, $url->getPath());
        $this->assertEquals(self::PATH, $url->getLocalPath());
    }

    /**
     * Test an invalid URL
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\InvalidArgumentException
     * @expectedExceptionCode 1449873819
     */
    public function testInvalidUrl()
    {
        new ObjectUrl('invalid://');
    }

    /**
     * Test an invalid URL path
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\InvalidArgumentException
     * @expectedExceptionCode 1449874494
     */
    public function testInvalidUrlPath()
    {
        new ObjectUrl('http://invalid~url*path', true);
    }

    /**
     * Test the scheme setter
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\InvalidArgumentException
     * @expectedExceptionCode 1449924914
     */
    public function testUrlSchemeSetter()
    {
        $url = new ObjectUrl(self::URL);
        $this->assertEquals(ObjectUrl::SCHEME_HTTPS, $url->setScheme(ObjectUrl::SCHEME_HTTPS)->getScheme());
        $url->setScheme('invalid');
    }

    /**
     * Test the host setter
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\InvalidArgumentException
     * @expectedExceptionCode 1449925567
     */
    public function testUrlHostSetter()
    {
        $url = new ObjectUrl(self::URL);
        $this->assertEquals('apparat.com', $url->setHost('apparat.com')->getHost());
        $url->setHost('_');
    }

    /**
     * Test the port setter
     *
     * @expectedException \Apparat\Object\Domain\Model\Path\InvalidArgumentException
     * @expectedExceptionCode 1449925885
     */
    public function testUrlPortSetter()
    {
        $url = new ObjectUrl(self::URL);
        $this->assertEquals(443, $url->setPort(443)->getPort());
        $url->setPort(123456789);
    }

    /**
     * Test the remaining setter methods
     */
    public function testUrlSetters()
    {
        $url = new ObjectUrl(self::URL);
        $this->assertEquals('test', $url->setUser('test')->getUser());
        $this->assertEquals(null, $url->setUser(null)->getUser());
        $this->assertEquals('password', $url->setPassword('password')->getPassword());
        $this->assertEquals(null, $url->setPassword(null)->getPassword());
        $this->assertEquals('/path/prefix', $url->setPath('/path/prefix')->getPath());
        $this->assertEquals(['param2' => 'value2'], $url->setQuery(['param2' => 'value2'])->getQuery());
        $this->assertEquals('fragment2', $url->setFragment('fragment2')->getFragment());

        $this->assertEquals(
            '2016-01-01',
            $url->setCreationDate(new \DateTimeImmutable('@1451606400'))->getCreationDate()->format('Y-m-d')
        );
        $this->assertEquals(123, $url->setId(new Id(123))->getId()->getId());
        $this->assertEquals('article', $url->setType(new Type('article'))->getType()->getType());
        $this->assertEquals(
            Revision::CURRENT,
            $url->setRevision(new Revision(Revision::CURRENT))->getRevision()->getRevision()
        );
    }

    /**
     * Test the override functionality when getting the URL path
     */
    public function testUrlPathOverride()
    {
        $url = new TestObjectUrl(self::URL);
        $this->assertEquals(
            'https://user:password@another.host:443/path/prefix/2015/10/01/36704.event/36704-2?param2=value2#fragment2',
            $url->getUrlOverride()
        );
    }

    /**
     * Test absolute URL
     */
    public function testUrlAbsolute()
    {
        $url = new ObjectUrl(self::REMOTE_URL, true);
        $this->assertEquals(true, $url->isAbsolute());
        $this->assertEquals(self::REMOTE_REPOSITORY_URL, $url->getRepositoryUrl());
    }

    /**
     * Test absolute URL
     */
    public function testUrlAbsoluteLocal()
    {
        $url = new ObjectUrl(rtrim(getenv('APPARAT_BASE_URL'), '/').self::REPOSITORY_URL.self::PATH, true);
        $this->assertTrue($url->isAbsoluteLocal());
    }

    /**
     * Test relative URL
     */
    public function testUrlRelative()
    {
        $url = new ObjectUrl(self::PATH.self::QUERY_FRAGMENT);
        $this->assertEquals(false, $url->isAbsolute());
    }

    /**
     * Test remote URL
     */
    public function testUrlRemote()
    {
        $url = new ObjectUrl(self::REMOTE_REPOSITORY_URL.self::REPOSITORY_URL.self::PATH, true);
        $this->assertTrue($url->isRemote());
        $url = new ObjectUrl(rtrim(getenv('APPARAT_BASE_URL'), '/').self::REPOSITORY_URL.self::PATH, true);
        $this->assertFalse($url->isRemote());
    }

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
     * Test object URL comparison
     */
    public function testObjectUrlComparison()
    {
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704.event/36704-1',
                true
            )
            )->matches(new ObjectUrl('https://example.com/2015/10/01/36704.event/36704-1', true))
        );
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704.event/36704-1',
                true
            )
            )->matches(new ObjectUrl('http://example.com/2016/10/01/36704.event/36704-1', true))
        );
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704.event/36704-1',
                true
            )
            )->matches(new ObjectUrl('http://example.com/2015/10/01/36705.event/36705-1', true))
        );
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704.event/36704-1',
                true
            )
            )->matches(new ObjectUrl('http://example.com/2015/10/01/36704.article/36704-1', true))
        );
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704.event/36704-1',
                true
            )
            )->matches(new ObjectUrl('http://example.com/2015/10/01/36704.event/36704-2', true))
        );
        $this->assertTrue((new ObjectUrl(self::REMOTE_URL, true))->matches(new ObjectUrl(self::REMOTE_URL, true)));
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
    public function testDraftPath() {
        $path = new LocalPath(self::DRAFT_PATH);
        $this->assertInstanceOf(LocalPath::class, $path);
        $this->assertTrue($path->isDraft());
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
}
