<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object
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

namespace Apparat\Object;

use Apparat\Kernel\Ports\AbstractModule;
use Apparat\Kernel\Ports\Contract\DependencyInjectionContainerInterface;
use Apparat\Object\Application\Contract\BinaryPayloadProcessorInterface;
use Apparat\Object\Application\Contract\CommonMarkPayloadProcessorInterface;
use Apparat\Object\Application\Model\Object\AbstractBinaryObject;
use Apparat\Object\Application\Model\Object\AbstractCommonMarkObject;
use Apparat\Object\Application\Model\Object\Manager;
use Apparat\Object\Domain\Model\Object\ManagerInterface;
use Apparat\Object\Domain\Repository\AdapterStrategyFactoryInterface;
use Apparat\Object\Domain\Repository\AutoConnectorInterface;
use Apparat\Object\Domain\Repository\Service;
use Apparat\Object\Infrastructure\Factory\AdapterStrategyFactory;
use Apparat\Object\Infrastructure\Repository\AutoConnector;
use Apparat\Object\Infrastructure\Utilities\BinaryPayloadProcessor;
use Apparat\Object\Infrastructure\Utilities\CommonMarkPayloadProcessor;
use Dotenv\Dotenv;

/**
 * Object module
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object
 */
class Module extends AbstractModule
{
    /**
     * Module name
     *
     * @var string
     */
    const NAME = 'object';

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Validate the environment
     *
     * @param Dotenv $environment Environment
     */
    protected static function validateEnvironment(Dotenv $environment)
    {
        parent::validateEnvironment($environment);

        // Validate the required environment variables
        $environment->required('APPARAT_BASE_URL')->notEmpty();
        $environment->required('OBJECT_RESOURCE_EXTENSION')->notEmpty();
        $environment->required('OBJECT_DATE_PRECISION')->isInteger()->allowedValues([0, 1, 2, 3, 4, 5, 6]);
        $environment->required('OBJECT_DEFAULT_LANGUAGE')->notEmpty();

        // In-depth validation of the apparat base URL
        $apparatBaseUrl = getenv('APPARAT_BASE_URL');
        self::isAbsoluteBareUrl($apparatBaseUrl);

        // Normalize the apparat base URL
        putenv('APPARAT_BASE_URL='.rtrim($apparatBaseUrl, '/').'/');
    }

    /**
     * Test whether a URL is absolute and doesn't have query parameters and / or a fragment
     *
     * @param string $url URL
     * @return boolean If the URL is absolute and has neither query parameters or a fragment
     * @throws \RuntimeException If the URL is not absolute / valid
     * @throws \RuntimeException If the URL has query parameters
     * @throws \RuntimeException If the URL has a fragment
     */
    public static function isAbsoluteBareUrl($url)
    {
        if (!filter_var($url) || !preg_match("%^https?\:\/\/%i", $url)) {
            throw new \RuntimeException(sprintf('Apparat base URL "%s" must be valid', $url), 1451776352);
        }
        if (strlen(parse_url($url, PHP_URL_QUERY))) {
            throw new \RuntimeException(
                sprintf('Apparat base URL "%s" must not contain query parameters', $url),
                1451776509
            );
        }
        if (strlen(parse_url($url, PHP_URL_FRAGMENT))) {
            throw new \RuntimeException(sprintf('Apparat base URL "%s" must not contain a fragment', $url), 1451776570);
        }

        return true;
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Configure the dependency injection container
     *
     * @param DependencyInjectionContainerInterface $diContainer Dependency injection container
     * @return void
     */
    public function configureDependencyInjection(DependencyInjectionContainerInterface $diContainer)
    {
        parent::configureDependencyInjection($diContainer);

        // Configure the repository service
        $diContainer->register(Service::class, [
            'shared' => true,
            'substitutions' => [
                AutoConnectorInterface::class => [
                    'instance' => AutoConnector::class,
                ],
                AdapterStrategyFactoryInterface::class => [
                    'instance' => AdapterStrategyFactory::class,
                ],
                ManagerInterface::class => [
                    'instance' => Manager::class,
                ],
            ]
        ]);

        // Configure the CommonMark payload processor
        $diContainer->register(AbstractCommonMarkObject::class, [
            'shared' => false,
            'substitutions' => [
                CommonMarkPayloadProcessorInterface::class => [
                    'instance' => CommonMarkPayloadProcessor::class,
                ],
            ]
        ]);

        // Configure the binary payload processor
        $diContainer->register(AbstractBinaryObject::class, [
            'shared' => false,
            'substitutions' => [
                BinaryPayloadProcessorInterface::class => [
                    'instance' => BinaryPayloadProcessor::class,
                ],
            ]
        ]);
    }
}
