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
 * Repository object path
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
     * Repository path constructor
     *
     * @param RepositoryInterface $repository Object repository this path applies to
     * @param null|string|LocatorInterface $path Object path
     */
    public function __construct(RepositoryInterface $repository, $path = null)
    {
        $this->repository = $repository;

        // If an instantiated path (local path, repository path, object URL) is given
        if ($path instanceof LocatorInterface) {
            $this->creationDate = $path->getCreationDate();
            $this->uid = $path->getId();
            $this->type = $path->getType();
            $this->revision = $path->getRevision();
            return;
        }

        // Else: Parse as string
        parent::__construct($path);
    }

    /**
     * Return the repository this path applies to
     *
     * @return RepositoryInterface Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Return the repository relative object path with a file extension
     *
     * @param string $extension File extension
     * @return string Repository relative object path with extension
     */
    public function withExtension($extension)
    {
        return $this.'.'.strtolower($extension);
    }
}
