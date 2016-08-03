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

namespace Apparat\Object\Domain\Model\Uri;

use Apparat\Object\Domain\Repository\RepositoryInterface;

/**
 * Repository object locator
 *
 * @package Apparat\Object
 * @subpackage Apparat\Object\Domain
 */
class RepositoryLocator extends Locator implements RepositoryLocatorInterface
{
    /**
     * Repository
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Repository locator constructor
     *
     * @param RepositoryInterface $repository Object repository this locator applies to
     * @param null|string|LocatorInterface $locator Object locator
     */
    public function __construct(RepositoryInterface $repository, $locator = null)
    {
        $this->repository = $repository;

        // If an instantiated locator (local locator, repository locator, object URL) is given
        if ($locator instanceof LocatorInterface) {
            $this->creationDate = $locator->getCreationDate();
            $this->uid = $locator->getId();
            $this->type = $locator->getObjectType();
            $this->revision = $locator->getRevision();
            return;
        }

        // Else: Parse as string
        parent::__construct($locator);
    }

    /**
     * Return the repository relative object locator with a file extension
     *
     * @param string $extension File extension
     * @return string Repository relative object locator with extension
     */
    public function withExtension($extension)
    {
        return $this.'.'.strtolower($extension);
    }

    /**
     * Serialize as repository URL
     *
     * @param bool $local Local URL only
     * @param bool $canonical Canonical URL only
     * @return string Repository URL
     */
    public function toRepositoryUrl($local = false, $canonical = false)
    {
        $repositoryUrl = $local ? '' : getenv('APPARAT_BASE_URL');
        $repositoryUrl .= $this->getRepository()->getUrl();
        return $repositoryUrl.$this->toUrl($canonical);
    }

    /**
     * Return the repository this locator applies to
     *
     * @return RepositoryInterface Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
