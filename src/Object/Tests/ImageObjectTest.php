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

namespace Apparat\Object\Tests {

    use Apparat\Object\Application\Model\Object\Image;
    use Apparat\Object\Infrastructure\Utilities\File;
    use Apparat\Object\Ports\Types\Object as ObjectTypes;

    /**
     * Image object tests
     *
     * @package Apparat\Object
     * @subpackage Apparat\Object\Test
     */
    class ImageObjectTest extends AbstractObjectTest
    {
        /**
         * Tears down the fixture
         */
        public function tearDown()
        {
            putenv('MOCK_COPY');
            parent::tearDown();
        }

        /**
         * Test the creation and persisting of an image object
         */
        public function testCreateAndPublishImageObject()
        {
            // Create a temporary repository
            $tempRepoDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo';
            $fixtureDirectory = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'non-repo'
                .DIRECTORY_SEPARATOR;
            $repository = $this->createRepository($tempRepoDirectory);
            $payloadFileName1 = '1.'.File::hash($fixtureDirectory.'MuehlenbergDerwitz.jpg').'.jpg';
            $payloadFileName2 = '1.'.File::hash($fixtureDirectory.'Normalsegelapparat1895.jpg').'.jpg';

            // Create and persist an image object
            $image = $repository->createObject(ObjectTypes::IMAGE, $fixtureDirectory.'MuehlenbergDerwitz.jpg')
                ->persist();
            $this->assertInstanceOf(Image::class, $image);
            $this->assertEquals($payloadFileName1, $image->getPayload());
            $this->assertFileExists(
                $tempRepoDirectory.dirname(str_replace('/', DIRECTORY_SEPARATOR, $image->getRepositoryLocator())).
                DIRECTORY_SEPARATOR.$payloadFileName1
            );

            // Publish and persist the image
            $image->publish()->persist();

            // Add content relevant properties, publish & persist
            $image->setDomain('license', 'gemeinfrei')->publish()->persist();

            // Alter the payload
            $image->setPayload($fixtureDirectory.'Normalsegelapparat1895.jpg')->persist();
            $this->assertEquals('gemeinfrei', $image->getDomain('license'));
            $this->assertEquals($payloadFileName2, $image->getPayload());
            $this->assertFileExists(
                $tempRepoDirectory.dirname(str_replace('/', DIRECTORY_SEPARATOR, $image->getRepositoryLocator())).
                DIRECTORY_SEPARATOR.$payloadFileName2
            );

            // Delete temporary repository
            $this->deleteRecursive($tempRepoDirectory);
        }

        /**
         * Test empty binary payload
         *
         * @expectedException \Apparat\Object\Ports\Exceptions\InvalidArgumentException
         * @expectedExceptionCode 1464296678
         */
        public function testEmptyBinaryPayload()
        {
            // Create a temporary repository
            $tempRepoDirectory = $this->registerTemporaryDirectory(sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo');
            $repository = $this->createRepository($tempRepoDirectory);

            // Create an image object
            $repository->createObject(ObjectTypes::IMAGE);
        }

        /**
         * Test empty binary payload
         *
         * @expectedException \Apparat\Object\Ports\Exceptions\RuntimeException
         * @expectedExceptionCode 1464299856
         */
        public function testFailedRepositoryImport()
        {
            putenv('MOCK_COPY=1');

            // Create a temporary repository
            $tempRepoDirectory = $this->registerTemporaryDirectory(sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo');
            $fixtureDirectory = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'non-repo'
                .DIRECTORY_SEPARATOR;
            $repository = $this->createRepository($tempRepoDirectory);

            // Create an image object
            $repository->createObject(ObjectTypes::IMAGE, $fixtureDirectory.'MuehlenbergDerwitz.jpg')->persist();
        }
    }
}

namespace Apparat\Object\Infrastructure\Repository {

    /**
     * Mocked version of the native copy() function
     *
     * @param string $source Path to the source file
     * @param string $dest The destination path
     * @return bool true on success or false on failure.
     */
    function copy($source, $dest)
    {
        return (getenv('MOCK_COPY') != 1) ? \copy($source, $dest) : false;
    }
}
