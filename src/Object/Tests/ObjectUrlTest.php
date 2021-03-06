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
use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Model\Uri\ObjectUrl;
use Apparat\Object\Domain\Repository\Service;
use Apparat\Object\Ports\Types\Object as ObjectTypes;

/**
 * Object URL tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Test
 */
class ObjectUrlTest extends AbstractDisabledAutoconnectorTest
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
     * Example locator
     *
     * @var string
     */
    const LOCATOR = '/2015/10/01/36704-event/36704-1';
    /**
     * Example locator (draft mode)
     *
     * @var string
     */
    const DRAFT_LOCATOR = '/2015/10/01/36704-event/.36704';
    /**
     * Example URL
     *
     * @var string
     */
    const URL = self::REPOSITORY_URL.self::LOCATOR.self::QUERY_FRAGMENT;
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
    const REMOTE_URL = self::REMOTE_REPOSITORY_URL.self::LOCATOR.self::QUERY_FRAGMENT;

    /**
     * Test an URL
     *
     * @expectedException \Apparat\Object\Domain\Model\Uri\InvalidArgumentException
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
        $this->assertEquals(['param' => 'value'], $url->getQueryParams());
        $this->assertEquals('param=value', $url->getQuery());
        $this->assertEquals('fragment', $url->getFragment());
        $this->assertInstanceOf(\DateTimeImmutable::class, $url->getCreationDate());
        $this->assertEquals('2015-10-01', $url->getCreationDate()->format('Y-m-d'));
        $this->assertInstanceOf(Id::class, $url->getId());
        $this->assertEquals(new Id(36704), $url->getId());
        $this->assertInstanceOf(Type::class, $url->getObjectType());
        $this->assertEquals(Kernel::create(Type::class, [ObjectTypes::EVENT]), $url->getObjectType());
        $this->assertInstanceOf(Revision::class, $url->getRevision());
        $this->assertEquals(new Revision(1), $url->getRevision());
        $this->assertEquals(self::REMOTE_REPOSITORY_URL, Service::normalizeRepositoryUrl($url));
        $this->assertFalse($url->isDraft());
        $this->assertTrue($url->setDraft(true)->isDraft());
    }

    /**
     * Test a remote draft URL
     */
    public function testRemoteDraftUrl()
    {
        $url = new ObjectUrl(self::REMOTE_REPOSITORY_URL.self::DRAFT_LOCATOR, true);
        $this->assertInstanceOf(ObjectUrl::class, $url);
        $this->assertTrue($url->isDraft());
    }

    /**
     * Test a local URL with path prefix
     */
    public function testLeadedLocalUrl()
    {
        $pathPrefix = '/prefix/path';
        $url = new ObjectUrl($pathPrefix.self::LOCATOR);
        $this->assertEquals($pathPrefix, $url->getPath());
        $this->assertEquals(self::LOCATOR, $url->getLocator());
        $this->assertEquals($pathPrefix.strtok(self::LOCATOR, '-'), $url->toUrl(true));
    }

    /**
     * Test an invalid URL
     *
     * @expectedException \Apparat\Object\Domain\Model\Uri\InvalidArgumentException
     * @expectedExceptionCode 1449873819
     */
    public function testInvalidUrl()
    {
        new ObjectUrl('invalid://');
    }

    /**
     * Test an invalid URL path
     *
     * @expectedException \Apparat\Object\Domain\Model\Uri\InvalidArgumentException
     * @expectedExceptionCode 1449874494
     */
    public function testInvalidUrlPath()
    {
        new ObjectUrl('http://invalid~url*path', true);
    }

    /**
     * Test the scheme setter
     *
     * @expectedException \Apparat\Object\Domain\Model\Uri\InvalidArgumentException
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
     * @expectedException \Apparat\Object\Domain\Model\Uri\InvalidArgumentException
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
     * @expectedException \Apparat\Object\Domain\Model\Uri\InvalidArgumentException
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
        /** @var Type $articleType */
        $articleType = Kernel::create(Type::class, [ObjectTypes::ARTICLE]);
        $url = new ObjectUrl(self::URL);
        $this->assertEquals('test', $url->setUser('test')->getUser());
        $this->assertEquals(null, $url->setUser(null)->getUser());
        $this->assertEquals('password', $url->setPassword('password')->getPassword());
        $this->assertEquals(null, $url->setPassword(null)->getPassword());
        $this->assertEquals('/path/prefix', $url->setPath('/path/prefix')->getPath());
        $this->assertEquals(['param2' => 'value2'], $url->setQueryParams(['param2' => 'value2'])->getQueryParams());
        $this->assertEquals('param=value', $url->setQuery('param=value')->getQuery());
        $this->assertEquals('fragment2', $url->setFragment('fragment2')->getFragment());

        $this->assertEquals(
            '2016-01-01',
            $url->setCreationDate(new \DateTimeImmutable('@1451606400'))->getCreationDate()->format('Y-m-d')
        );
        $this->assertEquals(123, $url->setId(new Id(123))->getId()->getId());
        $this->assertEquals(
            'article',
            $url->setObjectType($articleType)->getObjectType()->getType()
        );
        $this->assertTrue($url->setHidden(true)->isHidden());
        $this->assertEquals(
            Revision::CURRENT,
            $url->setRevision(Revision::current())->getRevision()->getRevision()
        );
    }

    /**
     * Test the override functionality when getting the URL path
     */
    public function testUrlPathOverride()
    {
        $url = new TestObjectUrl(self::URL);
        $this->assertEquals(
            'https://user:password@another.host:443/path/prefix/2015/10/01/36704-event/36704-2?param2=value2#fragment2',
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
        $url = new ObjectUrl(rtrim(getenv('APPARAT_BASE_URL'), '/').self::REPOSITORY_URL.self::LOCATOR, true);
        $this->assertTrue($url->isAbsoluteLocal());
    }

    /**
     * Test relative URL
     */
    public function testUrlRelative()
    {
        $url = new ObjectUrl(self::LOCATOR.self::QUERY_FRAGMENT);
        $this->assertEquals(false, $url->isAbsolute());
    }

    /**
     * Test remote URL
     */
    public function testUrlRemote()
    {
        $url = new ObjectUrl(self::REMOTE_REPOSITORY_URL.self::REPOSITORY_URL.self::LOCATOR, true);
        $this->assertTrue($url->isRemote());
        $url = new ObjectUrl(rtrim(getenv('APPARAT_BASE_URL'), '/').self::REPOSITORY_URL.self::LOCATOR, true);
        $this->assertFalse($url->isRemote());
    }

    /**
     * Test object URL comparison
     */
    public function testObjectUrlComparison()
    {
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704-event/36704-1',
                true
            )
            )->matches(new ObjectUrl('https://example.com/2015/10/01/36704-event/36704-1', true))
        );
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704-event/36704-1',
                true
            )
            )->matches(new ObjectUrl('http://example.com/2016/10/01/36704-event/36704-1', true))
        );
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704-event/36704-1',
                true
            )
            )->matches(new ObjectUrl('http://example.com/2015/10/01/36705-event/36705-1', true))
        );
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704-event/36704-1',
                true
            )
            )->matches(new ObjectUrl('http://example.com/2015/10/01/36704-article/36704-1', true))
        );
        $this->assertFalse(
            (
            new ObjectUrl(
                'http://example.com/2015/10/01/36704-event/36704-1',
                true
            )
            )->matches(new ObjectUrl('http://example.com/2015/10/01/36704-event/36704-2', true))
        );
        $this->assertTrue((new ObjectUrl(self::REMOTE_URL, true))->matches(new ObjectUrl(self::REMOTE_URL, true)));
    }
}
