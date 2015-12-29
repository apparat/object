<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framework
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

namespace Apparat\Object\Domain\Model\Path\Url {

	use Apparat\Object\Domain\Model\Path\Url;

	/**
	 * URL version with test extension
	 *
	 * @package Apparat\Resource
	 */
	class TestUrl extends Url
	{
		/**
		 * Test the URL getter with override parameters
		 */
		public function getUrlOverride()
		{
			return $this->_getUrl([
				'scheme' => Url::SCHEME_HTTPS,
				'user' => 'user',
				'pass' => 'password',
				'host' => 'another.host',
				'port' => 443,
				'path' => '/2015/10/01/36704.event/36704-2',
				'query' => ['param2' => 'value2'],
				'fragment' => 'fragment2',
			]);
		}
	}
}

namespace ApparatTest {

	use Apparat\Object\Domain\Model\Object\Id;
	use Apparat\Object\Domain\Model\Object\InvalidArgumentException;
	use Apparat\Object\Domain\Model\Object\Revision;
	use Apparat\Object\Domain\Model\Object\Type;
	use Apparat\Object\Domain\Model\Path\Url;

	/**
	 * Object URL tests
	 *
	 * @package Apparat\Object
	 * @subpackage ApparatTest
	 */
	class UrlTest extends AbstractTest
	{
		/**
		 * Example URL
		 *
		 * @var string
		 */
		const URL = 'http://apparat:tools@apparat.tools:80/2015/10/01/36704.event/36704-1?param=value#fragment';

		/**
		 * Test an URL
		 */
		public function testUrl()
		{
			$url = new Url(self::URL);
			$this->assertInstanceOf(Url::class, $url);
			$this->assertEquals(self::URL, strval($url));
			$this->assertEquals('http', $url->getScheme());
			$this->assertEquals('apparat', $url->getUser());
			$this->assertEquals('tools', $url->getPassword());
			$this->assertEquals('apparat.tools', $url->getHost());
			$this->assertEquals(80, $url->getPort());
			$this->assertEquals('/2015/10/01/36704.event/36704-1', $url->getPath());
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
		}

		/**
		 * Test an invalid URL
		 *
		 * @expectedException InvalidArgumentException
		 * @expectedExceptionCode 1449873819
		 */
		public function testInvalidUrl()
		{
			new Url('invalid://');
		}

		/**
		 * Test an invalid URL path
		 *
		 * @expectedException InvalidArgumentException
		 * @expectedExceptionCode 1449874494
		 */
		public function testInvalidUrlPath()
		{
			new Url('http://invalid~url*path');
		}

		/**
		 * Test the scheme setter
		 *
		 * @expectedException InvalidArgumentException
		 * @expectedExceptionCode 1449924914
		 */
		public function testUrlSchemeSetter()
		{
			$url = new Url(self::URL);
			$this->assertEquals(Url::SCHEME_HTTPS, $url->setScheme(Url::SCHEME_HTTPS)->getScheme());
			$url->setScheme('invalid');
		}

		/**
		 * Test the host setter
		 *
		 * @expectedException InvalidArgumentException
		 * @expectedExceptionCode 1449925567
		 */
		public function testUrlHostSetter()
		{
			$url = new Url(self::URL);
			$this->assertEquals('apparat.com', $url->setHost('apparat.com')->getHost());
			$url->setHost('_');
		}

		/**
		 * Test the port setter
		 *
		 * @expectedException InvalidArgumentException
		 * @expectedExceptionCode 1449925885
		 */
		public function testUrlPortSetter()
		{
			$url = new Url(self::URL);
			$this->assertEquals(443, $url->setPort(443)->getPort());
			$url->setPort(123456789);
		}

		/**
		 * Test the remaining setter methods
		 */
		public function testUrlSetters()
		{
			$url = new Url(self::URL);
			$this->assertEquals('test', $url->setUser('test')->getUser());
			$this->assertEquals(null, $url->setUser(null)->getUser());
			$this->assertEquals('password', $url->setPassword('password')->getPassword());
			$this->assertEquals(null, $url->setPassword(null)->getPassword());
			$this->assertEquals('/2015/10/01/36704.event/36704',
				$url->setPath('/2015/10/01/36704.event/36704')->getPath());
			$this->assertEquals(['param2' => 'value2'], $url->setQuery(['param2' => 'value2'])->getQuery());
			$this->assertEquals('fragment2', $url->setFragment('fragment2')->getFragment());

			$this->assertEquals('2016-01-01',
				$url->setCreationDate(new \DateTimeImmutable('@1451606400'))->getCreationDate()->format('Y-m-d'));
			$this->assertEquals(123, $url->setId(new Id(123))->getId()->getId());
			$this->assertEquals('article', $url->setType(new Type('article'))->getType()->getType());
			$this->assertEquals(Revision::CURRENT,
				$url->setRevision(new Revision(Revision::CURRENT))->getRevision()->getRevision());
		}

		/**
		 * Test the override functionality when getting the URL path
		 */
		public function testUrlPathOverride()
		{
			$url = new Url\TestUrl(self::URL);
			$this->assertEquals('https://user:password@another.host:443/2015/10/01/36704.event/36704-2?param2=value2#fragment2',
				$url->getUrlOverride());
		}
	}
}