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

namespace Apparat\Object\Domain\Model\Uri;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Application\Utility\ArrayUtility;
use Apparat\Object\Domain\Contract\SerializablePropertyInterface;
use Apparat\Object\Domain\Model\Uri\Traits\Psr7Trait;
use Psr\Http\Message\UriInterface;

/**
 * Object URL
 *
 * @package Apparat\Object\Domain\Model
 */
class Url extends Uri implements UriInterface, SerializablePropertyInterface
{
    /**
     * Use PSR-7 method
     */
    use Psr7Trait;
    /**
     * HTTP scheme
     *
     * @var string
     */
    const SCHEME_HTTP = 'http';
    /**
     * HTTPS schema
     *
     * @var string
     */
    const SCHEME_HTTPS = 'https';
    /**
     * Valid schemes
     *
     * @var array
     */
    protected static $schemes = [self::SCHEME_HTTP => true, self::SCHEME_HTTPS => true];
    /**
     * URL parts
     *
     * @var array
     */
    protected $urlParts = null;

    /**
     * URL constructor
     *
     * @param string $url URL
     * @throws InvalidArgumentException If the URL is invalid
     */
    public function __construct($url)
    {
        parent::__construct($url);

        // Parse the URL
        $this->urlParts = @parse_url($url);
        if ($this->urlParts === false) {
            throw new InvalidArgumentException(
                sprintf('Invalid URL "%s"', $url),
                InvalidArgumentException::INVALID_URL
            );
        }
    }

    /**
     * Unserialize the string representation of this property
     *
     * @param string $str Serialized property
     * @return SerializablePropertyInterface Property
     */
    public static function unserialize($str)
    {
        return Kernel::create(static::class, [$str]);
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
        return $this->getUrlInternal();
    }

    /**
     * Return the a complete serialized URL
     *
     * @param array $override Override components
     * @return string Serialized URL
     */
    protected function getUrlInternal(array &$override = [])
    {
        // Prepare the URL scheme
        $scheme = !empty($this->urlParts['scheme']) ? $this->getScheme().'://' : '';
        if (isset($override['scheme'])) {
            $scheme = trim($override['scheme']);
            if (strlen($scheme)) {
                $scheme .= '://';
            }
        }
        $override['scheme'] = $scheme;

        // Prepare the URL user
        $user = !empty($this->urlParts['user']) ? rawurlencode($this->getUser()) : '';
        if (isset($override['user'])) {
            $user = $override['user'];
        }
        $override['user'] = $user;

        // Prepare the URL password
        $pass = !empty($this->urlParts['pass']) ? ':'.rawurlencode($this->getPassword()) : '';
        if (isset($override['pass'])) {
            $pass = ':'.$override['pass'];
        }
        if ($user || $pass) {
            $pass .= '@';
        }
        $override['pass'] = $pass;

        // Prepare the URL host
        $host = !empty($this->urlParts['host']) ? $this->getHost() : '';
        if (isset($override['host'])) {
            $host = $override['host'];
        }
        $override['host'] = $host;

        // Prepare the URL port
        $port = !empty($this->urlParts['port']) ? ':'.$this->getPort() : '';
        if (isset($override['port'])) {
            $port = ':'.$override['port'];
        }
        $override['port'] = $port;

        // Prepare the URL path
        $path = empty($this->urlParts['path']) ? '' : $this->urlParts['path'];
        if (isset($override['path'])) {
            $path = $override['path'];
        }
        $override['path'] = $path;

        // Prepare the URL query
        $query = !empty($this->urlParts['query']) ? '?'.$this->urlParts['query'] : '';
        if (isset($override['query'])) {
            $query = (is_array($override['query']) ? http_build_query($override['query']) : strval($override['query']));
            if (strlen($query)) {
                $query = '?'.$query;
            }
        }
        $override['query'] = $query;

        // Prepare the URL fragment
        $fragment = !empty($this->urlParts['fragment']) ? '#'.$this->getFragment() : '';
        if (isset($override['fragment'])) {
            $fragment = $override['fragment'];
            if (strlen($fragment)) {
                $fragment = '#'.$fragment;
            }
        }
        $override['fragment'] = $fragment;

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * Return the URL user
     *
     * @return string|NULL URL user
     */
    public function getUser()
    {
        return isset($this->urlParts['user']) ? $this->urlParts['user'] : null;
    }

    /**
     * Return the URL password
     *
     * @return string|NULL URL password
     */
    public function getPassword()
    {
        return isset($this->urlParts['pass']) ? $this->urlParts['pass'] : null;
    }

    /**
     * Return the URL query parameters as list
     *
     * @return array URL query parameters
     */
    public function getQueryParams()
    {
        $query = [];
        if (isset($this->urlParts['query']) && !empty($this->urlParts['query'])) {
            parse_str($this->urlParts['query'], $query);
        }
        return ArrayUtility::sortRecursiveByKey((array)$query);
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
        if (preg_match("%[/\?#]%", $host) ||
            (!filter_var('http://'.$host, FILTER_VALIDATE_URL) && !filter_var($host, FILTER_VALIDATE_IP))
        ) {
            throw new InvalidArgumentException(
                sprintf('Invalid URL host "%s"', $host),
                InvalidArgumentException::INVALID_URL_HOST
            );
        }

        $url = clone $this;
        $url->urlParts['host'] = $host;
        return $url;
    }

    /**
     * Set the URL port
     *
     * @param int|null $port URL port
     * @return Url New URL
     * @throws InvalidArgumentException If the URL port is invalid
     */
    public function setPort($port)
    {
        // If the URL port is invalid
        if (is_int($port) ? (($port < 0) || ($port > 65535)) : ($port !== null)) {
            throw new InvalidArgumentException(
                sprintf('Invalid URL port "%s"', $port),
                InvalidArgumentException::INVALID_URL_PORT
            );
        }

        $url = clone $this;
        $url->urlParts['port'] = $port;
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
        $url->urlParts['user'] = $user ?: null;
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
        $url->urlParts['pass'] = $pass ?: null;
        return $url;
    }

    /**
     * Set the URL query
     *
     * @param string $query URL query
     * @return Url New URL
     */
    public function setQuery($query)
    {
        $url = clone $this;
        $url->urlParts['query'] = trim($query);
        return $url;
    }

    /**
     * Set the URL query parameters
     *
     * @param array $query URL query parameters
     * @return Url New URL
     */
    public function setQueryParams(array $query)
    {
        $url = clone $this;
        $url->urlParts['query'] = http_build_query($query);
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
        $url->urlParts['fragment'] = $fragment;
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
        return !empty($this->urlParts['scheme']) && !empty($this->urlParts['host']);
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
            $this->getPath(),
            strlen($apparatBaseUrlPath)
        );
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
        $url->urlParts['path'] = strlen($path) ? '/'.$path : null;
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
        if (strlen($scheme) && !array_key_exists(strtolower($scheme), static::$schemes)) {
            throw new InvalidArgumentException(
                sprintf('Invalid URL scheme "%s"', $scheme),
                InvalidArgumentException::INVALID_URL_SCHEME
            );
        }

        $url = clone $this;
        $url->urlParts['scheme'] = $scheme ? strtolower($scheme) : null;
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

    /**
     * Serialize the property
     *
     * @return mixed Property serialization
     */
    public function serialize()
    {
        return strval($this);
    }
}
