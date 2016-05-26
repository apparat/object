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

use Apparat\Object\Domain\Model\Object\Type;
use Apparat\Object\Domain\Repository\Repository;
use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;
use Apparat\Object\Ports\Repository as RepositoryFactory;

/**
 * Object tests
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Test
 */
class ImageObjectTest extends AbstractRepositoryEnabledTest
{
    /**
     * Tears down the fixture
     */
    public function tearDown()
    {
//        putenv('MOCK_RENAME');
        parent::tearDown();
    }

    /**
     * Test the creation and persisting of an image object
     */
    public function testCreateAndPublishImageObject()
    {
        // Create a temporary repository
        $tempRepoDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo';
        $repository = $this->createRepository($tempRepoDirectory);
        $source = __DIR__.DIRECTORY_SEPARATOR.'Fixture'.DIRECTORY_SEPARATOR.'non-repo'.DIRECTORY_SEPARATOR.
            'MuehlenbergDerwitz.jpg';

        $repository->createObject(Type::IMAGE, $source);

//echo $tempRepoDirectory;
//        $article = $this->createRepositoryAndArticleObject($tempRepoDirectory, $payload);
//        $this->assertInstanceOf(Article::class, $article);
//        $this->assertEquals($payload, $article->getPayload());
//        $this->assertFileExists($tempRepoDirectory.
//            str_replace('/', DIRECTORY_SEPARATOR, $article->getRepositoryPath()
//                ->withExtension(getenv('OBJECT_RESOURCE_EXTENSION'))));

        // Alter and persist the object
//        $article->setPayload('Revision 1 draft (updated)');
//        $article->persist();
//
//        // Publish and persist the first object revision
//        $article->setPayload('Revision 1');
//        $article->publish();
//        $article->persist();

        // Delete temporary repository
//        $this->deleteRecursive($tempRepoDirectory);
//        $article->persist();
    }

    /**
     * Create a temporary repository
     *
     * @param string $tempRepoDirectory Repository directory
     * @return Repository File repository
     */
    protected function createRepository($tempRepoDirectory)
    {
        $fileRepository = RepositoryFactory::create(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => $tempRepoDirectory,
            ]
        );
        $this->assertInstanceOf(Repository::class, $fileRepository);
        $this->assertEquals($fileRepository->getAdapterStrategy()->getRepositorySize(), 0);

        return $fileRepository;
    }

    /**
     * Recursively register a directory and all nested files and directories for deletion on teardown
     *
     * @param string $directory Directory
     */
    protected function deleteRecursive($directory)
    {
        $this->tmpFiles[] = $directory;
        foreach (scandir($directory) as $item) {
            if (!preg_match('%^\.+$%', $item)) {
                $path = $directory.DIRECTORY_SEPARATOR.$item;
                if (is_dir($path)) {
                    $this->deleteRecursive($path);
                    continue;
                }

                $this->tmpFiles[] = $path;
            }
        }
    }
}
