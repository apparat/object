<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Repository;

use Apparat\Object\Domain\Model\Object\ManagerInterface;
use Apparat\Object\Domain\Model\Path\ApparatUrl;
use Apparat\Object\Domain\Model\Path\ObjectUrl;
use Apparat\Object\Domain\Model\Path\Url;
use Apparat\Object\Module;

/**
 * Repository register
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class Service
{
    /**
     * Registered repositories
     *
     * @var array
     */
    protected static $_registry = [];
    /**
     * Repository auto-connector service
     *
     * @var AutoConnectorInterface
     */
    protected static $_autoConnector = null;
    /**
     * Adapter strategy factory
     *
     * @var AdapterStrategyFactoryInterface
     */
    protected static $_adapterStrategyFactory = null;
    /**
     * Object manager
     *
     * @var ManagerInterface
     */
    protected static $_objectManager = null;
    /**
     * Auto-connect to repositories
     *
     * @var bool
     */
    protected static $_autoConnectEnabled = true;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * Pre-register a repository
     *
     * The purpose of repository pre-registration is to provide custom arguments (like a base
     * directory or basic authentication credentials.
     * The repository URL may be local or remote, relative or absolute, with Apparat or HTTP scheme.
     *
     * @param string|Url $url Repository URL
     * @param RepositoryInterface $repository Repository
     */
    public static function register($url, RepositoryInterface $repository)
    {
        // Repository registration
        $repositoryUrl = self::normalizeRepositoryUrl($url);
        self::$_registry[$repositoryUrl] = $repository;
    }

    /**
     * Return an object repository by URL
     *
     * If a repository URL hasn't been pre-registered, the method tries to perform an adhoc registration
     * based on the URL given.
     * The repository URL may be local or remote, relative or absolute, with Apparat or HTTP scheme.
     *
     * @param string|Url $url Repository URL
     * @return \Apparat\Object\Domain\Repository\Repository Object repository
     * @throws InvalidArgumentException If the repository URL is invalid
     * @throws InvalidArgumentException If the repository URL is unknown
     */
    public static function get($url)
    {
        // Ensure that the service is properly configured
        self::isConfigured();

        $url = self::normalizeRepositoryUrl($url);

        // If the repository URL is unknown
        if (!self::_connected($url)) {
            throw new InvalidArgumentException(
                sprintf('Unknown repository URL "%s"', $url),
                InvalidArgumentException::UNKNOWN_REPOSITORY_URL
            );
        }

        // Return the repository instance
        return self::$_registry[$url];
    }

    /**
     * Test whether a repository URL is registered
     *
     * @param string $url Repository URL
     * @return bool Repository URL is registered
     */
    public static function isRegistered($url)
    {
        $url = self::normalizeRepositoryUrl($url);
        return array_key_exists($url, self::$_registry) || self::_connected($url);
    }

    /**
     * Configure the repository service
     *
     * @param AutoConnectorInterface|null $autoConnector Auto-connector
     * @param AdapterStrategyFactoryInterface|null $adapterStrategyFactory Adapter strategy factory
     * @param ManagerInterface|null $objectManager Object manager
     */
    public static function configure(
        AutoConnectorInterface $autoConnector = null,
        AdapterStrategyFactoryInterface $adapterStrategyFactory = null,
        ManagerInterface $objectManager = null
    ) {
        self::$_autoConnector = $autoConnector;
        self::$_adapterStrategyFactory = $adapterStrategyFactory;
        self::$_objectManager = $objectManager;
    }

    /**
     * Check whether the service is configured for auto-connecting to repositories
     *
     * @return bool Service is configured for auto-connecting
     * @throws RuntimeException If the service is not properly configured
     */
    public static function isConfigured()
    {
        if (
            !(self::$_autoConnector instanceof AutoConnectorInterface) ||
            !(self::$_adapterStrategyFactory instanceof AdapterStrategyFactoryInterface) ||
            !(self::$_objectManager instanceof ManagerInterface)
        ) {
            throw new RuntimeException(
                'The repository service is not configured',
                RuntimeException::SERVICE_NOT_CONFIGURED
            );
        }

        return true;
    }

    /**
     * Return the auto-connector service
     *
     * @return AutoConnectorInterface Auto-connector servie
     */
    public static function getAutoConnector()
    {
        return self::$_autoConnector;
    }

    /**
     * Return the adapter strategy factory
     *
     * @return AdapterStrategyFactoryInterface Adapter strategy factory
     */
    public static function getAdapterStrategyFactory()
    {
        return self::$_adapterStrategyFactory;
    }

    /**
     * Return the object manager
     *
     * @return ManagerInterface Object manager
     */
    public static function getObjectManager()
    {
        return self::$_objectManager;
    }

    /**
     * Normalize a repository URL
     *
     * @param string|Url $url Repository URL
     * @return string Normalized repository URL
     * @throws InvalidArgumentException If the repository URL is invalid
     */
    public static function normalizeRepositoryUrl($url)
    {
        // If it's an apparat URL
        if ($url instanceof ApparatUrl) {
            $url = $url->getNormalizedRepositoryUrl();

            // Else: If it's an object URL
        } elseif ($url instanceof ObjectUrl) {
            $url = $url->getRepositoryUrl();

            // Else: If it's a simple URL
        } elseif ($url instanceof Url) {
            $url = $url->getPath();

            // Else: If it's an empty URL
        } elseif ($url === null) {
            return '';
        }

        // If the URL is a string
        if (is_string($url)) {

            // Strip the leading apparat base URL
            $apparatBaseUrl = getenv('APPARAT_BASE_URL');
            if (strpos($url, $apparatBaseUrl) === 0) {
                $url = strval(substr($url, strlen($apparatBaseUrl)));
            }

            // Ensure this is a bare URL (without query and fragment)
            if (Module::isAbsoluteBareUrl($apparatBaseUrl.$url)) {
                return ltrim($url, '/');
            }
        }

        // The URL is invalid, throw an error
        throw new InvalidArgumentException(
            sprintf('Invalid repository URL "%s"', $url),
            InvalidArgumentException::INVALID_REPOSITORY_URL
        );
    }

    /**
     * Enable repository auto-connections
     *
     * If the method is called without any arguments it will just return the current auto-connection state.
     *
     * @param null|boolean $autoConnect Enable / disable auto-connections
     * @return bool Status of repository auto-connection
     */
    public static function useAutoConnect($autoConnect = null)
    {
        if ($autoConnect !== null) {
            self::$_autoConnectEnabled = (boolean)$autoConnect;
        }
        return self::$_autoConnectEnabled;
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Test whether a repository URL registered or can be auto-connected
     *
     * @param string $url Repository URL
     * @return bool Repository is connected
     */
    protected static function _connected($url)
    {
        // If the given repository URL is alredy registered: Success
        if (!empty(self::$_registry[$url])) {
            return true;
        }

        // If auto-connect is enabled and the service is properly configured: Try to auto-connect the repository
        return (self::isConfigured() && self::$_autoConnectEnabled && self::$_autoConnector->connect($url));
    }
}
