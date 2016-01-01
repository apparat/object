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
 *  this software and associated documentation files (the 'Software'), to deal in
 *  the Software without restriction, including without limitation the rights to
 *  use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 *  the Software, and to permit persons to whom the Software is furnished to do so,
 *  subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 *  FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 *  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 *  IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 *  CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 ***********************************************************************************/

namespace ApparatTest;

/**
 * Exception error handler
 *
 * @param int $severity Error severity
 * @param string $message Error message
 * @param string $file Error file
 * @param string $line Error line
 * @throws \ErrorException
 */
function exceptionErrorHandler($severity, $message, $file, $line)
{
	if (!(error_reporting() & $severity)) {
		return;
	}
	throw new \ErrorException($message, 0, $severity, $file, $line);
}

/**
 * Interceptor for fatal errors
 *
 * @throws \ErrorException
 */
function fatalErrorShutdownHandler()
{
	$last_error = error_get_last();
	if ($last_error['type'] === E_ERROR) {
		exceptionErrorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
	}
}

/**
 * Singleton tests
 *
 * @package Apparat\Object
 * @subpackage ApparatTest
 */
class SingletonTest extends AbstractTest
{
	/**'
	 * Setup
	 */
	public static function setUpBeforeClass()
	{
		set_error_handler('ApparatTest\exceptionErrorHandler');
		register_shutdown_function('ApparatTest\fatalErrorShutdownHandler');
		ini_set('display_errors', 'off');
		error_reporting(E_ALL);
	}

	/**
	 * Teardown
	 */
	public static function tearDownAfterClass()
	{
		set_error_handler(null);
	}

	/**
	 * Test illegal cloning of a singleton
	 */
	public function testSingletonClone()
	{
//		$mock = $this->getMockForTrait('Apparat\\Object\\Domain\\Contract\\SingletonTrait');
		try {
//			clone $mock;
		} catch (\ErrorException $e) {

		}
	}
}