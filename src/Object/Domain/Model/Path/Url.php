<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Model\Path;

use Apparat\Object\Application\Utility\ArrayUtility;

/**
 * Object URL
 *
 * @package Apparat\Object\Domain\Model
 */
class Url
{
    /**
     * HTTP-Schema
     *
     * @var string
     */
    const SCHEME_HTTP = 'http';
    /**
     * HTTPS-Schema
     *
     * @var string
     */
    const SCHEME_HTTPS = 'https';
    /**
     * Valid schemes
     *
     * @var array
     */
    protected static $_schemes = [self::SCHEME_HTTP => true, self::SCHEME_HTTPS => true];
    /**
     * URL parts
     *
     * @var array
     */
    protected $_urlParts = null;

    /*******************************************************************************
     * PUBLIC METHODS
     *******************************************************************************/

    /**
     * URL constructor
     *
     * @param string $url URL
     * @throws InvalidArgumentException If the URL is invalid
     */
    public function __construct($url)
    {

        // Parse the URL
        $this->_urlParts = @parse_url($url);
        if ($this->_urlParts === false) {
            throw new InvalidArgumentException(
                sprintf('Invalid URL "%s"', $url),
                InvalidArgumentException::INVALID_URL
            );
        }
    }

    /**
     * Return the serialized URL
     *
     * @return string Serialized URL
     */
    public function __toString()
    {
        return $this->getUrl();
    }

    /**
     * Return the full serialized URL
     *
     * @return string Full URL
     */
    public function getUrl()
    {
        return $this->_getUrl();
    }

    /**
     * Return the a complete serialized URL
     *
     * @param array $override Override components
     * @return string Serialized URL
     */
    protected function _getUrl(array &$override = [])
    {
        // Prepare the URL scheme
        if (isset($override['scheme'])) {
            $scheme = trim($override['scheme']);
            if (strlen($scheme)) {
                $scheme .= '://';
            }
        } else {
            $scheme = !empty($this->_urlParts['scheme']) ? $this->getScheme().'://' : '';
        }
        $override['scheme'] = $scheme;

        // Prepare the URL user
        if (isset($override['user'])) {
            $user = $override['user'];
        } else {
            $user = !empty($this->_urlParts['user']) ? rawurlencode($this->getUser()) : '';
        }
        $override['user'] = $user;

        // Prepare the URL password
        if (isset($override['pass'])) {
            $pass = ':'.$override['pass'];
        } else {
            $pass = !empty($this->_urlParts['pass']) ? ':'.rawurlencode($this->getPassword()) : '';
        }
        if ($user || $pass) {
            $pass .= '@';
        }
        $override['pass'] = $pass;

        // Prepare the URL host
        if (isset($override['host'])) {
            $host = $override['host'];
        } else {
            $host = !empty($this->_urlParts['host']) ? $this->getHost() : '';
        }
        $override['host'] = $host;

        // Prepare the URL port
        if (isset($override['port'])) {
            $port = ':'.$override['port'];
        } else {
            $port = !empty($this->_urlParts['port']) ? ':'.$this->getPort() : '';
        }
        $override['port'] = $port;

        // Prepare the URL path
        if (isset($override['path'])) {
            $path = $override['path'];
        } else {
            $path = empty($this->_urlParts['path']) ? '' : $this->_urlParts['path'];
        }
        $override['path'] = $path;

        // Prepare the URL query
        if (isset($override['query'])) {
            $query = (is_array($override['query']) ? http_build_query($override['query']) : strval($override['query']));
            if (strlen($query)) {
                $query = '?'.$query;
            }
        } else {
            $query = !empty($this->_urlParts['query']) ? '?'.$this->_urlParts['query'] : '';
        }
        $override['query'] = $query;

        // Prepare the URL fragment
        if (isset($override['fragment'])) {
            $fragment = $override['fragment'];
            if (strlen($fragment)) {
                $fragment = '#'.$fragment;
            }
        } else {
            $fragment = !empty($this->_urlParts['fragment']) ? '#'.$this->getFragment() : '';
        }
        $override['fragment'] = $fragment;

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * Return the URL scheme
     *
     * @return string URL scheme
     */
    public function getScheme()
    {
        return isset($this->_urlParts['scheme']) ? $this->_urlParts['scheme'] : null;
    }

    /**
     * Return the URL user
     *
     * @return string|NULL URL user
     */
    public function getUser()
    {
        return isset($this->_urlParts['user']) ? $this->_urlParts['user'] : null;
    }

    /**
     * Return the URL password
     *
     * @return string|NULL URL password
     */
    public function getPassword()
    {
        return isset($this->_urlParts['pass']) ? $this->_urlParts['pass'] : null;
    }

    /**
     * Return the URL host
     *
     * @return string URL host
     */
    public function getHost()
    {
        return isset($this->_urlParts['host']) ? $this->_urlParts['host'] : null;
    }

    /**
     * Return the URL port
     *
     * @return int URL port
     */
    public function getPort()
    {
        return isset($this->_urlParts['port']) ? $this->_urlParts['port'] : null;
    }

    /**
     * Return the URL fragment
     *
     * @return string URL fragment
     */
    public function getFragment()
    {
        return isset($this->_urlParts['fragment']) ? $this->_urlParts['fragment'] : null;
    }

    /**
     * Set the URL host
     *
     * @param string $host URL host
     * @return Url New URL
     * @throws InvalidArgumentException If the URL host is invalid
     */
    public function setHost($host)
    {
        // If the hostname is invalid
        if (preg_match("%[/\?#]%", $host) || (!filter_var('http://'.$host, FILTER_VALIDATE_URL) && !filter_var(
                    $host,
                    FILTER_VALIDATE_IP
                ))
        ) {
            throw new InvalidArgumentException(
                sprintf('Invalid URL host "%s"', $host),
                InvalidArgumentException::INVALID_URL_HOST
            );
        }

        $url = clone $this;
        $url->_urlParts['host'] = $host;
        return $url;
    }

    /**
     * Set the URL port
     *
     * @param int $port URL port
     * @return Url New URL
     * @throws InvalidArgumentException If the URL port is invalid
     */
    public function setPort($port)
    {
        // If the URL port is invalid
        if (!is_int($port) || ($port < 0) || ($port > 65535)) {
            throw new InvalidArgumentException(
                sprintf('Invalid URL port "%s"', $port),
                InvalidArgumentException::INVALID_URL_PORT
            );
        }

        $url = clone $this;
        $url->_urlParts['port'] = $port;
        return $url;
    }

    /**
     * Set the URL user
     *
     * @param string|NULL $user URL user
     * @return Url New URL
     */
    public function setUser($user)
    {
        $url = clone $this;
        $url->_urlParts['user'] = $user ?: null;
        return $url;
    }

    /**
     * Set the URL password
     *
     * @param string $pass URL password
     * @return Url New URL
     */
    public function setPassword($pass)
    {
        $url = clone $this;
        $url->_urlParts['pass'] = $pass ?: null;
        return $url;
    }

    /**
     * Set the URL query
     *
     * @param array $query URL query
     * @return Url New URL
     */
    public function setQuery(array $query)
    {
        $url = clone $this;
        $url->_urlParts['query'] = http_build_query($query);
        return $url;
    }

    /**
     * Set the URL fragment
     *
     * @param string $fragment URL fragment
     * @return Url New URL
     */
    public function setFragment($fragment)
    {
        $url = clone $this;
        $url->_urlParts['fragment'] = $fragment;
        return $url;
    }

    /**
     * Test whether this URL is remote
     *
     * @return bool Remote URL
     */
    public function isRemote()
    {
        return $this->isAbsolute() && !$this->isAbsoluteLocal();
    }

    /**
     * Return whether this URL is absolute
     *
     * @return bool Absolute URL
     */
    public function isAbsolute()
    {
        return !empty($this->_urlParts['scheme']) && !empty($this->_urlParts['host']);
    }

    /**
     * Test whether this URL belongs to the local Apparat instance
     *
     * @return bool URL belongs to the local Apparat instance
     */
    public function isAbsoluteLocal()
    {
        // Instantiate the apparat base URL
        $apparatBaseUrl = new self(getenv('APPARAT_BASE_URL'));
        $apparatBaseUrlPath = $apparatBaseUrl->getPath();
        $apparatBaseUrl = $apparatBaseUrl->setScheme(null)->setPath(null);

        // If the URL matches the Apparat base URL (the scheme is disregarded)
        return $this->isAbsolute() && $this->matches($apparatBaseUrl) && !strncmp(
            $apparatBaseUrlPath,
            $this->getPath(), strlen($apparatBaseUrlPath)
        );
    }

    /**
     * Return the URL path
     *
     * @return string URL path
     */
    public function getPath()
    {
        return $this->_urlParts['path'];
    }

    /**
     * Set the URL path
     *
     * @param string $path URL path
     * @return Url New URL
     */
    public function setPath($path)
    {
        $path = trim($path, '/');

        $url = clone $this;
        $url->_urlParts['path'] = strlen($path) ? '/'.$path : null;
        return $url;
    }

    /**
     * Set the URL scheme
     *
     * @param string $scheme URL scheme
     * @return Url New URL
     * @throws InvalidArgumentException If the URL scheme is invalid
     */
    public function setScheme($scheme)
    {
        // If the URL scheme is not valid
        if (strlen($scheme) && !array_key_exists($scheme, static::$_schemes)) {
            throw new InvalidArgumentException(
                sprintf('Invalid URL scheme "%s"', $scheme),
                InvalidArgumentException::INVALID_URL_SCHEME
            );
        }

        $url = clone $this;
        $url->_urlParts['scheme'] = $scheme;
        return $url;
    }

    /**
     * Test if this URL matches all available parts of a given URL
     *
     * @param Url $url Comparison URL
     * @return bool This URL matches all available parts of the given URL
     */
    public function matches(Url $url)
    {

        // Test the scheme
        $urlScheme = $url->getScheme();
        if (($urlScheme !== null) && ($this->getScheme() !== $urlScheme)) {
            return false;
        }

        // Test the user
        $urlUser = $url->getUser();
        if (($urlUser !== null) && ($this->getUser() !== $urlUser)) {
            return false;
        }

        // Test the password
        $urlPassword = $url->getPassword();
        if (($urlPassword !== null) && ($this->getPassword() !== $urlPassword)) {
            return false;
        }

        // Test the host
        $urlHost = $url->getHost();
        if (($urlHost !== null) && ($this->getHost() !== $urlHost)) {
            return false;
        }

        // Test the port
        $urlPort = $url->getPort();
        if (($urlPort !== null) && ($this->getPort() !== $urlPort)) {
            return false;
        }

        // Test the path
        $urlPath = $url->getPath();
        if (($urlPath !== null) && ($this->getPath() !== $urlPath)) {
            return false;
        }

        // Test the query
        $urlQuery = $url->getQuery();
        if (serialize($this->getQuery()) !== serialize($urlQuery)) {
            return false;
        }

        // Test the fragment
        $urlFragment = $url->getFragment();
        if (($urlFragment !== null) && ($this->getFragment() !== $urlFragment)) {
            return false;
        }

        return true;
    }

    /*******************************************************************************
     * PRIVATE METHODS
     *******************************************************************************/

    /**
     * Return the URL query
     *
     * @return array URL query
     */
    public function getQuery()
    {
        $query = [];
        if (isset($this->_urlParts['query']) && !empty($this->_urlParts['query'])) {
            @parse_str($this->_urlParts['query'], $query);
        }
        return ArrayUtility::sortRecursiveByKey($query);
    }
}
