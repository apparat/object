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

namespace Apparat\Object\Domain\Model\Path;

use Apparat\Kernel\Ports\Kernel;
use Apparat\Object\Domain\Repository\RepositoryInterface;
use Apparat\Object\Domain\Repository\Service;

/**
 * Apparat URL
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class ApparatUrl extends ObjectUrl
{
    /**
     * Valid schemes
     *
     * @var array
     */
    protected static $schemes = [self::SCHEME_APRT => true, self::SCHEME_APRTS => true];

    /**
     * APRT-Schema
     *
     * @var string
     */
    const SCHEME_APRT = 'aprt';
    /**
     * APRTS-Schema
     *
     * @var string
     */
    const SCHEME_APRTS = 'aprts';

    /**
     * Apparat URL constructor
     *
     * If the constructor doesn't throw an exception, the URL is valid and
     *
     * 1. either an absolute URL (local or remote) or
     * 2. a relative URL to a known local repository (respectively the to the context repository if given)
     *
     * @param string $url Apparat URL
     * @param boolean $remote Accept remote URL (less strict date component checking)
     * @param RepositoryInterface $contextRepository Context repository
     * @throws ApparatInvalidArgumentException If the URL is absolute but doesn't have the apparat scheme
     * @throws ApparatInvalidArgumentException If this is a local Apparat URL with an unknown repository
     */
    public function __construct($url, $remote = false, RepositoryInterface $contextRepository = null)
    {
        parent::__construct($url, $remote);

        // If it's an absolute URL
        if ($this->isAbsolute()) {
            // If the Apparat URL scheme is invalid
            if (!array_key_exists($this->urlParts['scheme'], self::$schemes)) {
                throw new ApparatInvalidArgumentException(
                    sprintf('Invalid absolute apparat URL "%s"', $url),
                    ApparatInvalidArgumentException::INVALID_ABSOLUTE_APPARAT_URL
                );
            }

            // Else: It's a relative URL
        } else {
            // If this URL doesn't have a repository URL and a context repository is given: Inherit its URL
            if (!strlen($this->getPath()) && ($contextRepository instanceof RepositoryInterface)) {
                $this->urlParts['path'] = $contextRepository->getUrl();
            }

            // If the the repository involved is unknown and cannot be auto-connected
            if (!Kernel::create(Service::class)->isRegistered($this->getPath())) {
                throw new ApparatInvalidArgumentException(
                    sprintf('Unknown local repository URL "%s"', $this->getPath()),
                    ApparatInvalidArgumentException::UNKNOWN_LOCAL_REPOSITORY_URL
                );
            };
        }
    }

    /**
     * Return the normalized repository URL part of this object URL
     *
     * @return string Repository URL
     */
    public function getNormalizedRepositoryUrl()
    {
        return preg_replace("%^aprt(s?)://%", "http\\1://", $this->getRepositoryUrl());
    }
}
