<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\<Layer>
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2016 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
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

namespace ApparatTest;

use Apparat\Object\Domain\Factory\SelectorFactory;
use Apparat\Object\Domain\Model\Object\Collection;
use Apparat\Object\Domain\Model\Path\RepositoryPath;
use Apparat\Object\Domain\Repository\InvalidArgumentException;
use Apparat\Object\Domain\Repository\Repository;
use Apparat\Object\Domain\Repository\SelectorInterface;
use Apparat\Object\Framework\Api\Repository as RepositoryFactory;
use Apparat\Object\Framework\Repository\AdapterStrategyFactory;
use Apparat\Object\Framework\Repository\FileAdapterStrategy;

/**
 * Repository test
 *
 * @package Apparat\Object
 * @subpackage ApparatTest
 */
class RepositoryTest extends AbstractTest
{
	/**
	 * Temporary glob directory
	 *
	 * @var string
	 */
	protected static $_globBase = null;
	/**
	 * Created temporary files
	 *
	 * @var array
	 */
	protected static $_globFiles = [];
	/**
	 * Created temporary directories
	 *
	 * @var array
	 */
	protected static $_globDirs = [];
	/**
	 * Type counter
	 *
	 * @var array
	 */
	protected static $_globTypes = ['event' => 0, 'article' => 0, 'note' => 0];
	/**
	 * Revision counter
	 *
	 * @var array
	 */
	protected static $_globRevisions = ['' => 0, '-0' => 0, '-1' => 0];

	/**
	 * Setup
	 */
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();
		self::$_globDirs[] =
		self::$_globBase = sys_get_temp_dir().DIRECTORY_SEPARATOR.'glob';

		$types = array_keys(self::$_globTypes);
		$revisions = array_keys(self::$_globRevisions);
		$index = 0;

		// Setup test directories & files
		for ($currentYear = intval(date('Y')), $year = $currentYear; $year < $currentYear + 3; ++$year) {
			self::$_globDirs[] =
			$yearDir = self::$_globBase.DIRECTORY_SEPARATOR.$year;
			for ($month = 1; $month < 13; ++$month) {
				self::$_globDirs[] =
				$monthDir = $yearDir.DIRECTORY_SEPARATOR.str_pad($month, 2, '0', STR_PAD_LEFT);
				$days = [];
				while (count($days) < 3) {
					$day = rand(1, date('t', mktime(0, 0, 0, $month, 1, $year)));
					$days[$day] = $day;
				}
				foreach ($days as $day) {
					self::$_globDirs[] =
					$dayDir = $monthDir.DIRECTORY_SEPARATOR.str_pad($day, 2, '0', STR_PAD_LEFT);
					mkdir($dayDir, 0777, true);
					self::$_globDirs[] =
					$hourDir = $dayDir.DIRECTORY_SEPARATOR.'00';
					mkdir($hourDir, 0777, true);
					self::$_globDirs[] =
					$minuteDir = $hourDir.DIRECTORY_SEPARATOR.'00';
					mkdir($minuteDir, 0777, true);
					self::$_globDirs[] =
					$secondDir = $minuteDir.DIRECTORY_SEPARATOR.'00';
					mkdir($secondDir, 0777, true);


					// Create random subfolders and object files
					for ($object = 1; $object < 3; ++$object) {
						++$index;
						$type = $types[rand(0, 2)];
						$revision = $revisions[rand(0, 2)];
						++self::$_globTypes[$type];
						++self::$_globRevisions[$revision];
						self::$_globDirs[] =
						$objectDir = $secondDir.DIRECTORY_SEPARATOR.$index.'.'.$type;
						mkdir($objectDir);
						self::$_globFiles[] =
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
		foreach (self::$_globFiles as $globFile) {
			@unlink($globFile);
		}
		foreach (array_reverse(self::$_globDirs) as $globDir) {
			@rmdir($globDir);
		}

		putenv('OBJECT_DATE_PRECISION=3');
	}

	/**
	 * Test empty repository config
	 *
	 * @expectedException \Apparat\Object\Framework\Api\InvalidArgumentException
	 * @expectedExceptionCode 1449947621
	 */
	public function testEmptyRepositoryConfig()
	{
		RepositoryFactory::create([]);
	}

	/**
	 * Test empty adapter strategy configuration
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449956347
	 */
	public function testEmptyAdapterStrategyConfig()
	{
		AdapterStrategyFactory::create([]);
	}

	/**
	 * Test invalid adapter strategy type
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449956471
	 */
	public function testInvalidAdapterStrategyType()
	{
		AdapterStrategyFactory::create([
			'url' => 'http://apparat',
			'type' => 'invalid',
		]);
	}

	/**
	 * Test file adapter strategy type
	 */
	public function testFileAdapterStrategy()
	{
		$fileAdapterStrategy = AdapterStrategyFactory::create([
			'url' => 'http://apparat',
			'type' => FileAdapterStrategy::TYPE,
			'root' => __DIR__,
		]);
		$this->assertInstanceOf(FileAdapterStrategy::class, $fileAdapterStrategy);
		$this->assertEquals(FileAdapterStrategy::TYPE, $fileAdapterStrategy->getType());
	}

	/**
	 * Test missing file adapter strategy root
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1450136346
	 */
	public function testMissingFileStrategyRoot()
	{
		RepositoryFactory::create([
			'url' => 'http://apparat',
			'type' => FileAdapterStrategy::TYPE,
		]);
	}

	/**
	 * Test empty file adapter strategy root
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449956977
	 */
	public function testEmptyFileStrategyRoot()
	{
		RepositoryFactory::create([
			'url' => 'http://apparat',
			'type' => FileAdapterStrategy::TYPE,
			'root' => '',
		]);
	}

	/**
	 * Test invalid file adapter strategy root
	 *
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionCode 1449957017
	 */
	public function testInvalidFileStrategyRoot()
	{
		RepositoryFactory::create([
			'url' => 'http://apparat',
			'type' => FileAdapterStrategy::TYPE,
			'root' => __FILE__,
		]);
	}

	/**
	 * Test file repository
	 */
	public function testFileRepository()
	{
		$fileRepository = RepositoryFactory::create([
			'url' => 'http://apparat',
			'type' => FileAdapterStrategy::TYPE,
			'root' => self::$_globBase,
		]);
		$this->assertInstanceOf(Repository::class, $fileRepository);

		$selector = SelectorFactory::createFromString('/*');
		$this->assertInstanceOf(SelectorInterface::class, $selector);
		$collection = $fileRepository->findObjects($selector);
		$this->assertInstanceOf(Collection::class, $collection);
		$this->assertEquals(array_sum(self::$_globTypes), count($collection));
	}

	/**
	 * Test file repository with revisions
	 */
	public function testFileRepositoryRevisions()
	{
		$fileRepository = RepositoryFactory::create([
			'url' => 'http://apparat',
			'type' => FileAdapterStrategy::TYPE,
			'root' => self::$_globBase,
		]);

		$selector = SelectorFactory::createFromString('/*/*/*/*/*/*/*.*/*-1');
		$collection = $fileRepository->findObjects($selector);
		$this->assertInstanceOf(Collection::class, $collection);
		$this->assertEquals(self::$_globRevisions['-1'], count($collection));
	}

	/**
	 * Test a repository path
	 */
	public function testRepositoryPath()
	{
		$fileRepository = RepositoryFactory::create([
			'url' => 'http://apparat',
			'type' => FileAdapterStrategy::TYPE,
			'root' => self::$_globBase,
		]);
		$repositoryPath = new RepositoryPath($fileRepository, '/2015/10/01/00/00/00/36704.event/36704-1');
		$this->assertInstanceOf(RepositoryPath::class, $repositoryPath);
		$this->assertEquals($fileRepository, $repositoryPath->getRepository());
	}
}