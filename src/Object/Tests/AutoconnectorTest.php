<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Tests
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

namespace Apparat\Object\Tests {

    use Apparat\Dev\Tests\AbstractTest;
    use Apparat\Kernel\Ports\Kernel;
    use Apparat\Object\Application\Model\Object\Contact;
    use Apparat\Object\Domain\Repository\Service;
    use Apparat\Object\Infrastructure\Repository\AutoConnector;
    use Apparat\Object\Infrastructure\Model\Object\Object;

    /**
     * Autoconnector tests
     *
     * @package Apparat\Object
     * @subpackage Apparat\Object\Tests
     */
    class AutoconnectorTest extends AbstractTest
    {
        /**
         * Example object locator
         *
         * @var string
         */
        const OBJECT_LOCATOR = '/2016/01/08/2-contact/2';

        /**
         * Setup
         */
        public static function setUpBeforeClass()
        {
            parent::setUpBeforeClass();
            Kernel::create(Service::class)->reset()->useAutoConnect(true);
        }

        /**
         * This method is called after the last test of this test class is run.
         */
        public static function tearDownAfterClass()
        {
            parent::tearDownAfterClass();
            Kernel::create(Service::class)->useAutoConnect(false);
        }

        /**
         * Tears down the fixture
         */
        public function tearDown()
        {
            putenv('MOCK_PHP_SAPI_NAME');
            Kernel::create(Service::class)->reset();
            parent::tearDown();
        }

        /**
         * Test basic auto-connect functionality with a relative URL and the CLI SAPI
         */
        public function testAutoconnectRelativeUrlCli()
        {
            $article = Object::load(self::OBJECT_LOCATOR);
            $this->assertInstanceOf(Contact::class, $article);
        }

        /**
         * Test basic auto-connect functionality with a relative URL and the webserver SAPI
         */
        public function testAutoconnectRelativeUrlWebserver()
        {
            putenv('MOCK_PHP_SAPI_NAME=1');
            $article = Object::load(self::OBJECT_LOCATOR);
            $this->assertInstanceOf(Contact::class, $article);
        }

        /**
         * Test basic auto-connection functionality
         *
         * @todo Implement when absolute repository is implemented
         */
        public function testAutoconnectAbsoluteUrl()
        {
            $autoconnector = new AutoConnector();
            $this->assertTrue($autoconnector->connect('http://example.com'.self::OBJECT_LOCATOR));
        }
    }
}

namespace Apparat\Object\Infrastructure\Repository {

    /**
     * Mocked version of the native php_sapiname() function
     *
     * @return string PHP SAPI
     */
    function php_sapi_name()
    {
        return (getenv('MOCK_PHP_SAPI_NAME') != 1) ? \php_sapi_name() : 'web';
    }
}
