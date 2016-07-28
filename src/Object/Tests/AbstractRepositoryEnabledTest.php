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

namespace Apparat\Object\Tests;

use Apparat\Object\Infrastructure\Repository\FileAdapterStrategy;
use Apparat\Object\Infrastructure\Repository\Repository;

/**
 * Abstract repository enabled test
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Tests
 */
abstract class AbstractRepositoryEnabledTest extends AbstractDisabledAutoconnectorTest
{
    /**
     * Test repository
     *
     * @var \Apparat\Object\Domain\Repository\Repository
     */
    protected static $repository = null;

    /**
     * Setup
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$repository = Repository::register(
            getenv('REPOSITORY_URL'),
            [
                'type' => FileAdapterStrategy::TYPE,
                'root' => __DIR__.DIRECTORY_SEPARATOR.'Fixture',
            ]
        );
        \date_default_timezone_set('UTC');
    }
}
