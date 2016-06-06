<?php

/**
 * apparat-object
 *
 * @category    Apparat
 * @package     Apparat\Object
 * @subpackage  Apparat\Object\Domain
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

namespace Apparat\Object\Domain\Model\Path\Traits;

use Apparat\Object\Domain\Model\Path\Url;

/**
 * PSR-7 URI trait
 *
 * @package Apparat\Object\Domain\Model\Path\Traits
 * @property array $urlParts
 */
trait Psr7Trait
{
    /**
     * Return the URL path
     *
     * @return string URL path
     */
    public function getPath()
    {
        return $this->urlParts['path'];
    }

    /**
     * Return the URL host
     *
     * @return string URL host
     */
    public function getHost()
    {
        return isset($this->urlParts['host']) ? $this->urlParts['host'] : null;
    }

    /**
     * Return the URL port
     *
     * @return int URL port
     */
    public function getPort()
    {
        return isset($this->urlParts['port']) ? $this->urlParts['port'] : null;
    }

    /**
     * Return the URL fragment
     *
     * @return string URL fragment
     */
    public function getFragment()
    {
        return isset($this->urlParts['fragment']) ? $this->urlParts['fragment'] : null;
    }

    /**
     * Return the URL query
     *
     * @return array URL query
     */
    public function getQuery()
    {
        return isset($this->urlParts['query']) ? $this->urlParts['query'] : '';
    }

    /**
     * Return the URL scheme
     *
     * @return string URL scheme
     */
    public function getScheme()
    {
        return isset($this->urlParts['scheme']) ? $this->urlParts['scheme'] : null;
    }

    /**
     * Return the URL authority
     *
     * @return string
     */
    public function getAuthority()
    {
        $uriParts = [];
        $this->getUrlInternal($uriParts);
        return $uriParts['user'].$uriParts['pass'].$uriParts['host'].$uriParts['port'];
    }

    /**
     * Return the URL user info
     *
     * @return string
     */
    public function getUserInfo()
    {
        $uriParts = [];
        $this->getUrlInternal($uriParts);
        return rtrim($uriParts['user'].$uriParts['pass'], '@');
    }

    /**
     * Return an instance of this URL with the given scheme
     *
     * @param string $scheme Scheme
     * @return Url Instance with the given scheme
     */
    public function withScheme($scheme)
    {
        return $this->setScheme($scheme);
    }

    /**
     * Return an instance of this URL with the given user info
     *
     * @param string $user User name
     * @param string|null $password Password
     * @return Url URL instance with given user info
     */
    public function withUserInfo($user, $password = null)
    {
        return $this->setUser($user)->setPassword($password);
    }

    /**
     * Return an instance of this URL with the given host
     *
     * @param string $host Host
     * @return Url Instance with the given host
     */
    public function withHost($host)
    {
        return $this->setHost($host);
    }

    /**
     * Return an instance of this URL with the given port
     *
     * @param null|int $port Port
     * @return Url Instance with the given port
     */
    public function withPort($port)
    {
        return $this->setPort($port);
    }

    /**
     * Return an instance of this URL with the given path
     *
     * @param null|int $path Path
     * @return Url Instance with the given path
     */
    public function withPath($path)
    {
        return $this->setPath($path);
    }

    /**
     * Return an instance of this URL with the given query
     *
     * @param null|string $query Query
     * @return Url Instance with the given query
     */
    public function withQuery($query)
    {
        return $this->setQuery($query);
    }

    /**
     * Return an instance of this URL with the given fragment
     *
     * @param null|int $fragment Fragment
     * @return Url Instance with the given fragment
     */
    public function withFragment($fragment)
    {
        return $this->setFragment($fragment);
    }

    /**
     * Set the URL scheme
     *
     * @param string $scheme URL scheme
     * @return Url New URL
     */
    abstract public function setScheme($scheme);

    /**
     * Set the URL user
     *
     * @param string|NULL $user URL user
     * @return Url New URL
     */
    abstract public function setUser($user);

    /**
     * Set the URL host
     *
     * @param string $host URL host
     * @return Url New URL
     */
    abstract public function setHost($host);

    /**
     * Set the URL port
     *
     * @param int|null $port URL port
     * @return Url New URL
     */
    abstract public function setPort($port);

    /**
     * Set the URL path
     *
     * @param string $path URL path
     * @return Url New URL
     */
    abstract public function setPath($path);

    /**
     * Set the URL query
     *
     * @param string $query URL query
     * @return Url New URL
     */
    abstract public function setQuery($query);

    /**
     * Set the URL fragment
     *
     * @param string $fragment URL fragment
     * @return Url New URL
     */
    abstract public function setFragment($fragment);

    /**
     * Return the a complete serialized URL
     *
     * @param array $override Override components
     * @return string Serialized URL
     */
    abstract protected function getUrlInternal(array &$override = []);
}
