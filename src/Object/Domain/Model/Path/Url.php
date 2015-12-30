<?php

/**
 * apparat-resource
 *
 * @category    Apparat
 * @package     Apparat\Object\Domain
 * @author      Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @copyright   Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
 * @license     http://opensource.org/licenses/MIT	The MIT License (MIT)
 */

/***********************************************************************************
 *  The MIT License (MIT)
 *
 *  Copyright © 2015 Joschi Kuphal <joschi@kuphal.net> / @jkphl
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

use Apparat\Object\Domain\Model\Object\Id;
use Apparat\Object\Domain\Model\Object\Revision;
use Apparat\Object\Domain\Model\Object\Type;

/**
 * Object URL
 *
 * @package Apparat\Object\Domain\Model
 */
class Url implements PathInterface
{
	/**
	 * URL parts
	 *
	 * @var array
	 */
	protected $_urlParts = null;
	/**
	 * Object path
	 *
	 * @var LocalPath
	 */
	protected $_path = null;

	/**
	 * Valid schemes
	 *
	 * @var array
	 */
	protected static $_schemes = [self::SCHEME_HTTP => true, self::SCHEME_HTTPS => true];

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

	/*******************************************************************************
	 * PUBLIC METHODS
	 *******************************************************************************/

	/**
	 * Object URL constructor
	 *
	 * @param string $url Object URL
	 * @param boolean $remote Accept remote URL (less strict date component checking)
	 * @throws InvalidArgumentException If the object URL is invalid
	 * @throws InvalidArgumentException If remote URLs are not allowed and a remote URL is given
	 */
	public function __construct($url, $remote = false)
	{

		// Parse the URL
		$this->_urlParts = @parse_url($url);
		if ($this->_urlParts === false) {
			throw new InvalidArgumentException(sprintf('Invalid object URL "%s"', $url),
				InvalidArgumentException::INVALID_OBJECT_URL);
		}

		// If it's an invalid remote object URL
		if ($this->isAbsolute() && !$remote) {
			throw new InvalidArgumentException(sprintf('Unallowed remote object URL "%s"', $url),
				InvalidArgumentException::UNALLOWED_REMOTE_OBJECT_URL);
		}

		$this->_path = new LocalPath(empty($this->_urlParts['path']) ? '' : $this->_urlParts['path'],
			$remote ? true : null);
	}

	/**
	 * Return the serialized object URL
	 *
	 * @return string Serialized object URL
	 */
	public function __toString()
	{
		return $this->getUrl();
	}

	/**
	 * Return the full serialized object URL
	 *
	 * @return string Full object URL
	 */
	public function getUrl()
	{
		return $this->_getUrl();
	}

	/**
	 * Return the object's creation date
	 *
	 * @return \DateTimeImmutable Object creation date
	 */
	public function getCreationDate()
	{
		return $this->_path->getCreationDate();
	}

	/**
	 * Set the object's creation date
	 *
	 * @param \DateTimeImmutable $creationDate
	 * @return LocalPath New object path
	 */
	public function setCreationDate(\DateTimeImmutable $creationDate)
	{
		$this->_path = $this->_path->setCreationDate($creationDate);
		return $this;
	}

	/**
	 * Return the object type
	 *
	 * @return Type Object type
	 */
	public function getType()
	{
		return $this->_path->getType();
	}

	/**
	 * Set the object type
	 *
	 * @param Type $type Object type
	 * @return Url New object URL
	 */
	public function setType(Type $type)
	{
		$this->_path = $this->_path->setType($type);
		return $this;
	}

	/**
	 * Return the object ID
	 *
	 * @return Id Object ID
	 */
	public function getId()
	{
		return $this->_path->getId();
	}

	/**
	 * Set the object ID
	 *
	 * @param Id $id Object ID
	 * @return Url New object URL
	 */
	public function setId(Id $id)
	{
		$this->_path = $this->_path->setId($id);
		return $this;
	}


	/**
	 * Return the object revision
	 *
	 * @return Revision Object revision
	 */
	public function getRevision()
	{
		return $this->_path->getRevision();
	}

	/**
	 * Set the object revision
	 *
	 * @param Revision $revision Object revision
	 * @return Url New object URL
	 */
	public function setRevision(Revision $revision)
	{
		$this->_path = $this->_path->setRevision($revision);
		return $this;
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
	 * Set the URL scheme
	 *
	 * @param string $scheme URL scheme
	 * @return Url New object URL
	 * @throws InvalidArgumentException If the URL scheme is invalid
	 */
	public function setScheme($scheme)
	{
		// If the URL scheme is not valid
		if (strlen($scheme) && !array_key_exists($scheme, static::$_schemes)) {
			throw new InvalidArgumentException(sprintf('Invalid object URL scheme "%s"', $scheme),
				InvalidArgumentException::INVALID_OBJECT_URL_SCHEME);
		}

		$url = clone $this;
		$url->_urlParts['scheme'] = $scheme;
		return $url;
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
	 * Set the URL host
	 *
	 * @param string $host URL host
	 * @return Url New object URL
	 * @throws InvalidArgumentException If the URL host is invalid
	 */
	public function setHost($host)
	{
		// If the hostname is invalid
		if (preg_match("%[/\?#]%", $host) || (!filter_var('http://'.$host, FILTER_VALIDATE_URL) && !filter_var($host,
					FILTER_VALIDATE_IP))
		) {
			throw new InvalidArgumentException(sprintf('Invalid object URL host "%s"', $host),
				InvalidArgumentException::INVALID_OBJECT_URL_HOST);
		}

		$url = clone $this;
		$url->_urlParts['host'] = $host;
		return $url;
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
	 * Set the URL port
	 *
	 * @param int $port URL port
	 * @return Url New object URL
	 * @throws InvalidArgumentException If the URL port is invalid
	 */
	public function setPort($port)
	{
		// If the URL port is invalid
		if (!is_int($port) || ($port < 0) || ($port > 65535)) {
			throw new InvalidArgumentException(sprintf('Invalid object URL port "%s"', $port),
				InvalidArgumentException::INVALID_OBJECT_URL_PORT);
		}

		$url = clone $this;
		$url->_urlParts['port'] = $port;
		return $url;
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
	 * Set the URL user
	 *
	 * @param string|NULL $user URL user
	 * @return Url New object URL
	 */
	public function setUser($user)
	{
		$url = clone $this;
		$url->_urlParts['user'] = $user ?: null;
		return $url;
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
	 * Set the URL password
	 *
	 * @param string $pass URL password
	 * @return Url New object URL
	 */
	public function setPassword($pass)
	{
		$url = clone $this;
		$url->_urlParts['pass'] = $pass ?: null;
		return $url;
	}

	/**
	 * Return the URL path
	 *
	 * @return string URL path
	 */
	public function getPath()
	{
		return strval($this->_path);
	}

	/**
	 * Set the URL path
	 *
	 * @param string $path URL path
	 * @return Url New object URL
	 */
	public function setPath($path)
	{
		$this->_path = new LocalPath(strval($path));
		return $this;
	}

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
		return $query;
	}

	/**
	 * Set the URL query
	 *
	 * @param array $query URL query
	 * @return Url New object URL
	 */
	public function setQuery(array $query)
	{
		$url = clone $this;
		$url->_urlParts['query'] = http_build_query($query);
		return $url;
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
	 * Set the URL fragment
	 *
	 * @param string $fragment URL fragment
	 * @return Url New object URL
	 */
	public function setFragment($fragment)
	{
		$url = clone $this;
		$url->_urlParts['fragment'] = $fragment;
		return $url;
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

	/*******************************************************************************
	 * PRIVATE METHODS
	 *******************************************************************************/

	/**
	 * Return the a complete serialized object URL
	 *
	 * @param array $override Override componentes
	 * @return string Serialized URL
	 */
	protected function _getUrl(array $override = [])
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

		// Prepare the URL user
		if (isset($override['user'])) {
			$user = $override['user'];
		} else {
			$user = !empty($this->_urlParts['user']) ? rawurlencode($this->getUser()) : '';
		}

		// Prepare the URL password
		if (isset($override['pass'])) {
			$pass = ':'.$override['pass'];
		} else {
			$pass = !empty($this->_urlParts['pass']) ? ':'.rawurlencode($this->getPassword()) : '';
		}
		if ($user || $pass) {
			$pass .= '@';
		}

		// Prepare the URL host
		if (isset($override['host'])) {
			$host = $override['host'];
		} else {
			$host = !empty($this->_urlParts['host']) ? $this->getHost() : '';
		}

		// Prepare the URL port
		if (isset($override['port'])) {
			$port = ':'.$override['port'];
		} else {
			$port = !empty($this->_urlParts['port']) ? ':'.$this->getPort() : '';
		}

		// Prepare the URL path
		if (isset($override['path'])) {
			$path = $override['path'];
		} else {
			$path = strval($this->_path);
		}

		// Prepare the URL query
		if (isset($override['query'])) {
			$query = '?'.(is_array($override['query']) ? http_build_query($override['query']) : strval($override['query']));
		} else {
			$query = !empty($this->_urlParts['query']) ? '?'.$this->_urlParts['query'] : '';
		}

		// Prepare the URL fragment
		if (isset($override['fragment'])) {
			$fragment = '#'.$override['fragment'];
		} else {
			$fragment = !empty($this->_urlParts['fragment']) ? '#'.$this->getFragment() : '';
		}

		return "$scheme$user$pass$host$port$path$query$fragment";
	}
}