<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Framework
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

namespace Apparat\Object\Framework;

// Instantiate Dotenv
use Apparat\Object\Application\Model\Object\Manager;
use Apparat\Object\Framework\Factory\AdapterStrategyFactory;
use Apparat\Object\Framework\Repository\AutoConnector;

$dotenv = new \Dotenv\Dotenv(dirname(dirname(dirname(__DIR__))));
if (getenv('APP_ENV') === 'development') {
    $dotenv->load();
}

// Validate the required environment variables
$dotenv->required('APPARAT_BASE_URL')->notEmpty();
$dotenv->required('OBJECT_RESOURCE_EXTENSION')->notEmpty();
$dotenv->required('OBJECT_DATE_PRECISION')->isInteger()->allowedValues([0, 1, 2, 3, 4, 5, 6]);

// In-depth validation of the apparat base URL
$apparatBaseUrl = getenv('APPARAT_BASE_URL');
\Apparat\Object\Domain\isAbsoluteBareUrl($apparatBaseUrl);

// Normalize the apparat base URL
putenv('APPARAT_BASE_URL='.rtrim($apparatBaseUrl, '/').'/');

// Unset global variables
unset($dotenv);
unset($apparatBaseUrl);

// Configure the repository service
\Apparat\Object\Domain\Repository\Service::configure(new AutoConnector(), new AdapterStrategyFactory(), new Manager());
