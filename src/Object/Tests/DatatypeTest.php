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
use Apparat\Object\Application\Model\Properties\Datatype\ApparatUrl;
use Apparat\Object\Application\Model\Properties\Datatype\Datetime;
use Apparat\Object\Application\Model\Properties\Datatype\Email;
use Apparat\Object\Application\Model\Properties\Datatype\Geo;
use Apparat\Object\Application\Model\Properties\Datatype\Integer;
use Apparat\Object\Application\Model\Properties\Datatype\Number;
use Apparat\Object\Application\Model\Properties\Datatype\Sentence;
use Apparat\Object\Application\Model\Properties\Datatype\Text;
use Apparat\Object\Application\Model\Properties\Datatype\Token;
use Apparat\Object\Application\Model\Properties\Datatype\Url;
use Apparat\Object\Domain\Model\Object\ObjectInterface;
use Apparat\Object\Domain\Model\Path\GeoUri;
use Apparat\Object\Ports\Object;

/**
 * Property model tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
class DatatypeTest extends AbstractRepositoryEnabledTest
{
    /**
     * Object
     *
     * @var ObjectInterface
     */
    protected static $object;
    /**
     * Example object path
     *
     * @var string
     */
    const ARTICLE_PATH = '/2015/12/21/1-article/1';
    /**
     * Example Url
     *
     * @var string
     */
    const URL = 'http://example.com/path/to/resource?var=val';

    /**
     * Setup
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$object = Object::instance(getenv('REPOSITORY_URL').self::ARTICLE_PATH);
    }

    /**
     * Test the apparat URL data type without valid filters
     *
     * @expectedException \InvalidArgumentException
     */
    public function testApparatUrlDataTypeWithoutFilter()
    {
        /** @var ApparatUrl $apparatUrlDatatype */
        $apparatUrlDatatype = Kernel::create(ApparatUrl::class, [self::$object, ['invalid']]);
        $this->assertInstanceOf(ApparatUrl::class, $apparatUrlDatatype);
        $apparatUrlDatatype->match(self::ARTICLE_PATH);
    }

    /**
     * Test the URL data type with an invalid value
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     */
    public function testUrlDataTypeWithInvalidUrl()
    {
        /** @var Url $urlDatatype */
        $urlDatatype = Kernel::create(Url::class, [self::$object, []]);
        $this->assertInstanceOf(Url::class, $urlDatatype);
        $this->assertInstanceOf(\Apparat\Object\Domain\Model\Path\Url::class, $urlDatatype->match(self::URL));
        $urlDatatype->match('http://');
    }

    /**
     * Test the Sentence data type with a multiline text
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     */
    public function testSentenceDataTypeWithMultilineText()
    {
        /** @var Sentence $sentenceDatatype */
        $sentenceDatatype = Kernel::create(Sentence::class, [self::$object, []]);
        $this->assertInstanceOf(Sentence::class, $sentenceDatatype);
        $this->assertEquals('Test sentence', $sentenceDatatype->match('Test sentence'));
        $sentenceDatatype->match("Test\nsentence");
    }

    /**
     * Test the Email data type with an invalid email address
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     */
    public function testEmailDataTypeWithInvalidEmail()
    {
        /** @var Email $emailDatatype */
        $emailDatatype = Kernel::create(Email::class, [self::$object, []]);
        $this->assertInstanceOf(Email::class, $emailDatatype);
        $this->assertEquals('john@doe.com', $emailDatatype->match('john@doe.com'));
        $emailDatatype->match('invalid-email');
    }

    /**
     * Test the Token data type with whitespace
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     */
    public function testTokenDataTypeWithWhitespace()
    {
        /** @var Token $tokenDatatype */
        $tokenDatatype = Kernel::create(Token::class, [self::$object, []]);
        $this->assertInstanceOf(Token::class, $tokenDatatype);
        $this->assertEquals('token', $tokenDatatype->match('token'));
        $tokenDatatype->match('invalid token');
    }

    /**
     * Test the Text data type
     */
    public function testTextDataType()
    {
        /** @var Text $textDatatype */
        $textDatatype = Kernel::create(Text::class, [self::$object, []]);
        $this->assertInstanceOf(Text::class, $textDatatype);
        $this->assertEquals("Text\nwith\nseveral\nlines", $textDatatype->match("Text\nwith\nseveral\nlines"));
    }

    /**
     * Test the Datetime data type with a timestamp
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     */
    public function testDatetimeDataType()
    {
        $now = new \DateTimeImmutable();
        $datetimeDatatype = Kernel::create(Datetime::class, [self::$object, []]);
        $this->assertInstanceOf(Datetime::class, $datetimeDatatype);
        $this->assertEquals($now, $datetimeDatatype->match($now->format('c')));
        $datetimeDatatype->match(time());
    }

    /**
     * Test the Geo data type with an invalid Geo URI
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     */
    public function testGeoDataTypeWithInvalidGeoUrl()
    {
        $geoUriStr = 'geo:1.23,-4.56,678.9';
        /** @var GeoUri $geoUri */
        $geoUri = Kernel::create(GeoUri::class, [$geoUriStr]);
        $geoApparatUrlStr = '/2016/06/05/1-geo';
        /** @var \Apparat\Object\Domain\Model\Path\ApparatUrl $geoApparatUrl */
        $geoApparatUrl = Kernel::create(
            \Apparat\Object\Domain\Model\Path\ApparatUrl::class,
            [$geoApparatUrlStr, false, self::$object->getRepositoryPath()->getRepository()]
        );

        /** @var Geo $geoDatatype */
        $geoDatatype = Kernel::create(Geo::class, [self::$object, []]);
        $this->assertInstanceOf(Geo::class, $geoDatatype);
        $this->assertEquals($geoUri, $geoDatatype->match($geoUriStr));
        $this->assertTrue($geoApparatUrl->matches($geoDatatype->match($geoApparatUrlStr)));
        $geoDatatype->match('Invalid Geo URI');
    }

    /**
     * Test the Integer data type with a string
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     */
    public function testIntegerDataTypeWithString()
    {
        /** @var \Apparat\Object\Application\Model\Properties\Datatype\Integer $integerDatatype */
        $integerDatatype = Kernel::create(Integer::class, [self::$object, []]);
        $this->assertInstanceOf(Integer::class, $integerDatatype);
        $this->assertEquals(100, $integerDatatype->match('100'));
        $integerDatatype->match('string');
    }

    /**
     * Test the Number data type with a string
     *
     * @expectedException \Apparat\Object\Application\Model\Properties\Domain\DomainException
     */
    public function testNumberDataTypeWithString()
    {
        /** @var \Apparat\Object\Application\Model\Properties\Datatype\Number $numberDatatype */
        $numberDatatype = Kernel::create(Number::class, [self::$object, []]);
        $this->assertInstanceOf(Number::class, $numberDatatype);
        $this->assertEquals(123.45, $numberDatatype->match('123.45'));
        $numberDatatype->match('string');
    }
}
