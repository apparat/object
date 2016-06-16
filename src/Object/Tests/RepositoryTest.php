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

use Apparat\Object\Domain\Factory\SelectorFactory;
use Apparat\Object\Domain\Model\Object\Collection;
use Apparat\Object\Domain\Model\Uri\RepositoryLocator;
use Apparat\Object\Domain\Repository\Repository;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Object\Infrastructure\Factory\AdapterStrategyFactory;
use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;
use Apparat\Object\Infrastructure\Repository\Repository as InfrastructureRepository;
use Apparat\Object\Module;

/**
 * Repository test
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Test
 */
class RepositoryTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Temporary glob directory
     *
     * @var string
     */
    protected static $globBase = null;
    /**
     * Created temporary files
     *
     * @var array
     */
    protected static $globFiles = [];
    /**
     * Created temporary directories
     *
     * @var array
     */
    protected static $globDirs = [];
    /**
     * Type counter
     *
     * @var array
     */
    protected static $globTypes = ['event' => 0, 'article' => 0, 'note' => 0];
    /**
     * Revision counter
     *
     * @var array
     */
    protected static $globRevisions = ['' => 0, '-0' => 0, '-1' => 0];

    /**
     * Setup
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$globDirs[] =
        self::$globBase = sys_get_temp_dir().DIRECTORY_SEPARATOR.'glob';

        $types = array_keys(self::$globTypes);
        $revisions = array_keys(self::$globRevisions);
        $index = 0;

        // Setup test directories & files
        for ($currentYear = intval(date('Y')), $year = $currentYear; $year < $currentYear + 3; ++$year) {
            self::$globDirs[] =
            $yearDir = self::$globBase.DIRECTORY_SEPARATOR.$year;
            for ($month = 1; $month < 13; ++$month) {
                self::$globDirs[] =
                $monthDir = $yearDir.DIRECTORY_SEPARATOR.str_pad($month, 2, '0', STR_PAD_LEFT);
                $days = [];
                while (count($days) < 3) {
                    $day = rand(1, date('t', mktime(0, 0, 0, $month, 1, $year)));
                    $days[$day] = $day;
                }
                foreach ($days as $day) {
                    self::$globDirs[] =
                    $dayDir = $monthDir.DIRECTORY_SEPARATOR.str_pad($day, 2, '0', STR_PAD_LEFT);
                    mkdir($dayDir, 0777, true);
                    self::$globDirs[] =
                    $hourDir = $dayDir.DIRECTORY_SEPARATOR.'00';
                    mkdir($hourDir, 0777, true);
                    self::$globDirs[] =
                    $minuteDir = $hourDir.DIRECTORY_SEPARATOR.'00';
                    mkdir($minuteDir, 0777, true);
                    self::$globDirs[] =
                    $secondDir = $minuteDir.DIRECTORY_SEPARATOR.'00';
                    mkdir($secondDir, 0777, true);


                    // Create random subfolders and object files
                    for ($object = 1; $object < 3; ++$object) {
                        ++$index;
                        $type = $types[rand(0, 2)];
                        $revision = $revisions[rand(0, 2)];
                        ++self::$globTypes[$type];
                        ++self::$globRevisions[$revision];
                        self::$globDirs[] =
                        $objectDir = $secondDir.DIRECTORY_SEPARATOR.$index.'-'.$type;
                        mkdir($objectDir);
                        self::$globFiles[] =
                        $objectFile = $objectDir.DIRECTORY_SEPARATOR.$index.$revision;
                        touch($objectFile);
                    }
                }
            }
        }

        putenv('OBJECT_DATE_PRECISION=6');
    }

    /**
     * Teardown
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        foreach (self::$globFiles as $globFile) {
            unlink($globFile);
        }
        foreach (array_reverse(self::$globDirs) as $globDir) {
            rmdir($globDir);
        }

        putenv('OBJECT_DATE_PRECISION=3');
    }

    /**
     * Test a bare URL with invalid schema
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1451776352
     */
    public static function testInvalidSchemaBareUrl()
    {
        Module::isAbsoluteBareUrl('ftp://example.com');
    }

    /**
     * Test a bare URL with query
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1451776509
     */
    public static function testInvalidQueryBareUrl()
    {
        Module::isAbsoluteBareUrl('http://example.com/?a=1');
    }

    /**
     * Test a bare URL with fragment
     *
     * @expectedException \RuntimeException
     * @expectedExceptionCode 1451776570
     */
    public static function testInvalidFragmentBareUrl()
    {
        Module::isAbsoluteBareUrl('http://example.com/#1');
    }

    /**
     * Test invalid query repository URL registration
     *
     * @expectedException \Apparat\Object\Infrastructure\Repository\InvalidArgumentException
     * @expectedExceptionCode 1451776509
     */
    public function testRegisterInvalidQueryRepositoryUrl()
    {
        InfrastructureRepository::register(getenv('REPOSITORY_URL').'?a=1', []);
    }

    /**
     * Test invalid query repository URL registration
     *
     * @expectedException \Apparat\Object\Infrastructure\Repository\InvalidArgumentException
     * @expectedExceptionCode 1451776509
     */
    public function testInstantiateInvalidQueryRepositoryUrl()
    {
        InfrastructureRepository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => __DIR__,
            ]
        );
        InfrastructureRepository::instance(getenv('REPOSITORY_URL').'?a=1');
    }

    /**
     * Test unknown public repository URL instantiation
     *
     * @expectedException \Apparat\Object\Infrastructure\Repository\InvalidArgumentException
     * @expectedExceptionCode 1451771889
     */
    public function testInstantiateUnknownRepositoryUrl()
    {
        InfrastructureRepository::instance('unknown');
    }

    /**
     * Test empty repository config
     *
     * @expectedException \Apparat\Object\Infrastructure\Factory\InvalidArgumentException
     * @expectedExceptionCode 1449956347
     */
    public function testEmptyRepositoryConfig()
    {
        InfrastructureRepository::register(getenv('REPOSITORY_URL'), []);
    }

    /**
     * Test empty adapter strategy configuration
     *
     * @expectedException \Apparat\Object\Infrastructure\Factory\InvalidArgumentException
     * @expectedExceptionCode 1449956347
     */
    public function testEmptyAdapterStrategyConfig()
    {
        (new AdapterStrategyFactory)->createFromConfig([]);
    }

    /**
     * Test invalid adapter strategy type
     *
     * @expectedException \Apparat\Object\Infrastructure\Factory\InvalidArgumentException
     * @expectedExceptionCode 1449956471
     */
    public function testInvalidAdapterStrategyType()
    {
        (new AdapterStrategyFactory)->createFromConfig(
            [
                'type' => 'invalid',
            ]
        );
    }

    /**
     * Test file adapter strategy type
     */
    public function testFileAdapterStrategy()
    {
        $fileAdapterStrategy = (new AdapterStrategyFactory)->createFromConfig(
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => __DIR__,
            ]
        );
        $this->assertInstanceOf(FileAdapterStrategy::class, $fileAdapterStrategy);
        $this->assertEquals(FileAdapterStrategy::TYPE, $fileAdapterStrategy->getType());
    }

    /**
     * Test invalid file adapter strategy root
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1450136346
     */
    public function testMissingFileStrategyRoot()
    {
        InfrastructureRepository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
            ]
        );
        InfrastructureRepository::instance(getenv('REPOSITORY_URL'));
    }

    /**
     * Test empty file adapter strategy root
     *
     * @expectedException \Apparat\Object\Infrastructure\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449956977
     */
    public function testEmptyFileStrategyRoot()
    {
        InfrastructureRepository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => '',
            ]
        );
        InfrastructureRepository::instance(getenv('REPOSITORY_URL'));
    }

    /**
     * Test invalid file adapter strategy root
     *
     * @expectedException \Apparat\Object\Infrastructure\Repository\InvalidArgumentException
     * @expectedExceptionCode 1449957017
     */
    public function testInvalidFileStrategyRoot()
    {
        InfrastructureRepository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => '__FILE__',
            ]
        );
        InfrastructureRepository::instance(getenv('REPOSITORY_URL'));
    }

    /**
     * Test invalid repository URL during instantiation
     *
     * @expectedException \Apparat\Object\Domain\Repository\InvalidArgumentException
     * @expectedExceptionCode 1451771889
     */
    public function testUnknownRepositoryUrlInstance()
    {
        InfrastructureRepository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => self::$globBase,
            ]
        );
        InfrastructureRepository::instance('http://example.com');
    }

    /**
     * Test file repository
     */
    public function testFileRepository()
    {
        InfrastructureRepository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => self::$globBase,
            ]
        );
        $fileRepository = InfrastructureRepository::instance(getenv('REPOSITORY_URL'));
        $this->assertInstanceOf(Repository::class, $fileRepository);

        $selector = SelectorFactory::createFromString('/*');
        $this->assertInstanceOf(SelectorInterface::class, $selector);
        $collection = $fileRepository->findObjects($selector);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(array_sum(self::$globTypes), count($collection));
        $this->assertEquals(0, $fileRepository->getSize());
    }

    /**
     * Test file repository with revisions
     */
    public function testFileRepositoryRevisions()
    {
        InfrastructureRepository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => self::$globBase,
            ]
        );
        $fileRepository = InfrastructureRepository::instance(getenv('REPOSITORY_URL'));

        $selector = SelectorFactory::createFromString('/*/*/*/*/*/*/*-*/*-1');
        $collection = $fileRepository->findObjects($selector);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(self::$globRevisions['-1'], count($collection));
    }

    /**
     * Test a repository locator
     */
    public function testRepositoryLocator()
    {
        InfrastructureRepository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => self::$globBase,
            ]
        );
        $fileRepository = InfrastructureRepository::instance(getenv('REPOSITORY_URL'));
        $repositoryLocator = new RepositoryLocator($fileRepository, '/2015/10/01/00/00/00/36704-event/36704-1');
        $this->assertInstanceOf(RepositoryLocator::class, $repositoryLocator);
        $this->assertEquals($fileRepository, $repositoryLocator->getRepository());
    }

    /**
     * Test the creation of a new repository
     */
    public function testRepositoryCreation()
    {
        $this->tmpFiles[] = $tempRepoDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo';
        $this->tmpFiles[] = $tempRepoConfigDir = $tempRepoDirectory.DIRECTORY_SEPARATOR.'.repo';
        $this->tmpFiles[] = $tempRepoConfigDir.DIRECTORY_SEPARATOR.'size.txt';
        $fileRepository = InfrastructureRepository::create(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => $tempRepoDirectory,
            ]
        );
        $this->assertInstanceOf(Repository::class, $fileRepository);
    }

    /**
     * Test creation of a repository over an existing file
     *
     * @expectedException \Apparat\Object\Domain\Repository\RuntimeException
     * @expectedExceptionCode 1461276430
     */
    public function testRepositoryCreationOverExistingFile()
    {
        $tempFile = $this->createTemporaryFile();
        InfrastructureRepository::create(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => $tempFile,
            ]
        );
    }

    /**
     * Test creation of a repository over an existing size descriptor directory
     *
     * @expectedException \Apparat\Object\Domain\Repository\RuntimeException
     * @expectedExceptionCode 1461276603
     */
    public function testRepositoryCreationOverExistingSizeDescriptor()
    {
        $this->tmpFiles[] = $tempRepoDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'temp-repo';
        $this->tmpFiles[] = $tempRepoConfigDir = $tempRepoDirectory.DIRECTORY_SEPARATOR.'.repo';
        $this->tmpFiles[] = $tempSizeDescriptor = $tempRepoConfigDir.DIRECTORY_SEPARATOR.'size.txt';
        mkdir($tempSizeDescriptor, 0777, true);
        InfrastructureRepository::create(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => $tempRepoDirectory,
            ]
        );
    }
}
